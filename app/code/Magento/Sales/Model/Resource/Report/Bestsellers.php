<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bestsellers report resource model
 */
namespace Magento\Sales\Model\Resource\Report;

class Bestsellers extends \Magento\Sales\Model\Resource\Report\AbstractReport
{
    const AGGREGATION_DAILY   = 'daily';
    const AGGREGATION_MONTHLY = 'monthly';
    const AGGREGATION_YEARLY  = 'yearly';

    /**
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_productResource;

    /**
     * @var \Magento\Sales\Model\Resource\Helper
     */
    protected $_salesResourceHelper;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Sales\Model\Resource\Helper $salesResourceHelper
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Sales\Model\Resource\Helper $salesResourceHelper
    ) {
        parent::__construct($logger, $resource, $locale, $reportsFlagFactory);
        $this->_productResource = $productResource;
        $this->_salesResourceHelper = $salesResourceHelper;
    }


    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('sales_bestsellers_aggregated_' . self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return \Magento\Sales\Model\Resource\Report\Bestsellers
     * @throws \Exception
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from    = $this->_dateToUtc($from);
        $to      = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $adapter = $this->_getWriteAdapter();
        //$this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales_flat_order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $this->getTable('sales_flat_order')),
                    'source_table.created_at', $from, $to
                )
            );
            $select = $adapter->select();

            $select->group(array(
                $periodExpr,
                'source_table.store_id',
                'order_item.product_id'
            ));

            $columns = array(
                'period'                 => $periodExpr,
                'store_id'               => 'source_table.store_id',
                'product_id'             => 'order_item.product_id',
                'product_name'           => new \Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                    'product_name.value',
                    'product_default_name.value'
                ))),
                'product_price'          => new \Zend_Db_Expr(
                    sprintf(
                        '%s * %s',
                        new \Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                            $adapter->getIfNullSql('product_price.value', 'product_default_price.value'), 0
                        ))),
                        new \Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                            'source_table.base_to_global_rate',
                            '0'
                        )))
                    )
                ),
                'qty_ordered' => new \Zend_Db_Expr('SUM(order_item.qty_ordered)')
            );

            $select->from(array('source_table' => $this->getTable('sales_flat_order')), $columns)
                ->joinInner(array(
                    'order_item' => $this->getTable('sales_flat_order_item')),
                    'order_item.order_id = source_table.entity_id',
                    array()
                )
                ->where('source_table.state != ?', \Magento\Sales\Model\Order::STATE_CANCELED);

            $productTypes = array(
                \Magento\Catalog\Model\Product\Type::TYPE_GROUPED,
                \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
                \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            );

            $joinExpr = array(
                'product.entity_id = order_item.product_id',
                $adapter->quoteInto('product.entity_type_id = ?', $this->_productResource->getTypeId()),
                $adapter->quoteInto('product.type_id NOT IN(?)', $productTypes)
            );

            $joinExpr = implode(' AND ', $joinExpr);
            $select->joinInner(
                array('product' => $this->getTable('catalog_product_entity')),
                $joinExpr,
                array()
            );

            // join product attributes Name & Price
            $attr = $this->_productResource->getAttribute('name');
            $joinExprProductName = array(
                'product_name.entity_id = product.entity_id',
                'product_name.store_id = source_table.store_id',
                $adapter->quoteInto('product_name.entity_type_id = ?', $this->_productResource->getTypeId()),
                $adapter->quoteInto('product_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductName = implode(' AND ', $joinExprProductName);
            $joinProductName = array(
                'product_default_name.entity_id = product.entity_id',
                'product_default_name.store_id = 0',
                $adapter->quoteInto('product_default_name.entity_type_id = ?', $this->_productResource->getTypeId()),
                $adapter->quoteInto('product_default_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinProductName = implode(' AND ', $joinProductName);
            $select->joinLeft(
                array('product_name' => $attr->getBackend()->getTable()),
                $joinExprProductName,
                array()
            )
            ->joinLeft(
                array('product_default_name' => $attr->getBackend()->getTable()),
                $joinProductName,
                array()
            );
            $attr = $this->_productResource->getAttribute('price');
            $joinExprProductPrice = array(
                'product_price.entity_id = product.entity_id',
                'product_price.store_id = source_table.store_id',
                $adapter->quoteInto('product_price.entity_type_id = ?', $this->_productResource->getTypeId()),
                $adapter->quoteInto('product_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductPrice = implode(' AND ', $joinExprProductPrice);

            $joinProductPrice = array(
                'product_default_price.entity_id = product.entity_id',
                'product_default_price.store_id = 0',
                $adapter->quoteInto('product_default_price.entity_type_id = ?', $this->_productResource->getTypeId()),
                $adapter->quoteInto('product_default_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinProductPrice = implode(' AND ', $joinProductPrice);
            $select->joinLeft(
                array('product_price' => $attr->getBackend()->getTable()),
                $joinExprProductPrice,
                array()
            )
            ->joinLeft(
                array('product_default_price' => $attr->getBackend()->getTable()),
                $joinProductPrice,
                array()
            );

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->useStraightJoin();  // important!
            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($insertQuery);


            $columns = array(
                'period'        => 'period',
                'store_id'      => new \Zend_Db_Expr(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID),
                'product_id'    => 'product_id',
                'product_name'  => new \Zend_Db_Expr('MIN(product_name)'),
                'product_price' => new \Zend_Db_Expr('MIN(product_price)'),
                'qty_ordered'   => new \Zend_Db_Expr('SUM(qty_ordered)'),
            );

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array('period', 'product_id'));
            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($insertQuery);

            // update rating
            $this->_updateRatingPos(self::AGGREGATION_DAILY);
            $this->_updateRatingPos(self::AGGREGATION_MONTHLY);
            $this->_updateRatingPos(self::AGGREGATION_YEARLY);
            $this->_setFlagData(\Magento\Reports\Model\Flag::REPORT_BESTSELLERS_FLAG_CODE);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of \Magento\Sales\Model\Resource\Report\Bestsellers::AGGREGATION_XXX constants
     * @return \Magento\Sales\Model\Resource\Report\Bestsellers
     */
    protected function _updateRatingPos($aggregation)
    {
        $aggregationTable   = $this->getTable('sales_bestsellers_aggregated_' . $aggregation);

        $aggregationAliases = array(
            'daily'   => self::AGGREGATION_DAILY,
            'monthly' => self::AGGREGATION_MONTHLY,
            'yearly'  => self::AGGREGATION_YEARLY
        );
        $this->_salesResourceHelper->getBestsellersReportUpdateRatingPos(
            $aggregation,
            $aggregationAliases,
            $this->getMainTable(),
            $aggregationTable
        );
        return $this;
    }
}
