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
 * Adminhtml sales order create sidebar cart block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Sidebar;

class Reorder extends \Magento\Adminhtml\Block\Sales\Order\Create\Sidebar\AbstractSidebar
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_order_item';

    /**
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $_ordersFactory;

    /**
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $ordersFactory
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\Resource\Order\CollectionFactory $ordersFactory,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = array()
    ) {
        $this->_ordersFactory = $ordersFactory;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $salesConfig, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_reorder');
        $this->setDataId('reorder');
    }


    public function getHeaderText()
    {
        return __('Last Ordered Items');
    }

    /**
     * Retrieve last order on current website
     *
     * @return \Magento\Sales\Model\Order|false
     */
    public function getLastOrder()
    {
        $storeIds = $this->getQuote()->getStore()->getWebsite()->getStoreIds();
        $collection = $this->_ordersFactory->create()
            ->addFieldToFilter('customer_id', $this->getCustomerId())
            ->addFieldToFilter('store_id', array('in' => $storeIds))
            ->setOrder('created_at', 'desc')
            ->setPageSize(1)
            ->load();
        foreach ($collection as $order) {
            return $order;
        }

        return false;
    }
    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        if ($order = $this->getLastOrder()) {
            $items = array();
            foreach ($order->getItemsCollection() as $item) {
                if (!$item->getParentItem()) {
                    $items[] = $item;
                }
            }
            return $items;
        }
        return false;
    }

    public function canDisplayItemQty()
    {
        return false;
    }

    public function canRemoveItems()
    {
        return false;
    }

    public function canDisplayPrice()
    {
        return false;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param \Magento\Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }
}
