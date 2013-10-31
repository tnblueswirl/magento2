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
 * @package     Magento_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml product grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Review\Product;

class Grid extends \Magento\Adminhtml\Block\Catalog\Product\Grid
{
    /**
     * @var \Magento\Core\Model\Resource\Website\CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory
     * @param \Magento\Core\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Resource\Website\CollectionFactory $websitesFactory,
        \Magento\Core\Model\WebsiteFactory $websiteFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_websitesFactory = $websitesFactory;
        parent::__construct(
            $websiteFactory, $setsFactory, $productFactory, $type, $status, $visibility, $catalogData, $coreData,
            $context, $storeManager, $urlModel, $data
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
                'header'    => __('ID'),
                'width'     => '50px',
                'index'     => 'entity_id',
        ));

        $this->addColumn('name', array(
                'header'    => __('Name'),
                'index'     => 'name',
        ));

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                    'header'    => __('Product Store Name'),
                    'index'     => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
                'header'    => __('SKU'),
                'width'     => '80px',
                'index'     => 'sku'
        ));

        $this->addColumn('price', array(
                'header'    => __('Price'),
                'type'      => 'currency',
                'index'     => 'price'
        ));

        $this->addColumn('qty', array(
                'header'    => __('Quantity'),
                'width'     => '130px',
                'type'      => 'number',
                'index'     => 'qty'
        ));

        $this->addColumn('status', array(
                'header'    => __('Status'),
                'width'     => '90px',
                'index'     => 'status',
                'type'      => 'options',
                'source'    => 'Magento\Catalog\Model\Product\Status',
                'options'   => $this->_status->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> __('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => $this->_websitesFactory->create()->toOptionHash(),
            ));
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        return $this;
    }
}
