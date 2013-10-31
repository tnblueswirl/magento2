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
 * @package     Magento_Reports
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Event observer model
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Event;

class Observer
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reports\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ComparedFactory
     */
    protected $_productCompFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ViewedFactory
     */
    protected $_productIndxFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Log\Model\Visitor
     */
    protected $_logVisitor;

    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Reports\Model\EventFactory $event,
        \Magento\Reports\Model\Product\Index\ComparedFactory $productCompFactory,
        \Magento\Reports\Model\Product\Index\ViewedFactory $productIndxFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Log\Model\Visitor $logVisitor
    ) {
        $this->_storeManager = $storeManager;
        $this->_eventFactory = $event;
        $this->_productCompFactory = $productCompFactory;
        $this->_productIndxFactory = $productIndxFactory;
        $this->_customerSession = $customerSession;
        $this->_logVisitor = $logVisitor;
    }
    /**
     * Abstract Event obeserver logic
     *
     * Save event
     *
     * @param int $eventTypeId
     * @param int $objectId
     * @param int $subjectId
     * @param int $subtype
     * @return \Magento\Reports\Model\Event\Observer
     */
    protected function _event($eventTypeId, $objectId, $subjectId = null, $subtype = 0)
    {
        if (is_null($subjectId)) {
            if ($this->_customerSession->isLoggedIn()) {
                $customer = $this->_customerSession->getCustomer();
                $subjectId = $customer->getId();
            }
            else {
                $subjectId = $this->_logVisitor->getId();
                $subtype = 1;
            }
        }

        $eventModel = $this->_eventFactory->create();
        $storeId    = $this->_storeManager->getStore()->getId();
        $eventModel
            ->setEventTypeId($eventTypeId)
            ->setObjectId($objectId)
            ->setSubjectId($subjectId)
            ->setSubtype($subtype)
            ->setStoreId($storeId);
        $eventModel->save();

        return $this;
    }

    /**
     * Customer login action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function customerLogin(\Magento\Event\Observer $observer)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return $this;
        }

        $visitorId  = $this->_logVisitor->getId();
        $customerId = $this->_customerSession->getCustomerId();
        $eventModel = $this->_eventFactory->create();
        $eventModel->updateCustomerType($visitorId, $customerId);

        $this->_productCompFactory
            ->create()
            ->updateCustomerFromVisitor()
            ->calculate();
        $this->_productIndxFactory
            ->create()
            ->updateCustomerFromVisitor()
            ->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function customerLogout(\Magento\Event\Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->purgeVisitorByCustomer()
            ->calculate();
        $this->_productIndxFactory
            ->create()
            ->purgeVisitorByCustomer()
            ->calculate();
        return $this;
    }

    /**
     * View Catalog Product action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function catalogProductView(\Magento\Event\Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        $this->_productIndxFactory
            ->create()
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(\Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW, $productId);
    }

    /**
     * Send Product link to friends action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function sendfriendProduct(\Magento\Event\Observer $observer)
    {
        return $this->_event(\Magento\Reports\Model\Event::EVENT_PRODUCT_SEND,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Remove Product from Compare Products action
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function catalogProductCompareRemoveProduct(\Magento\Event\Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->calculate();

        return $this;
    }

    /**
     * Remove All Products from Compare Products
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function catalogProductCompareClear(\Magento\Event\Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->calculate();

        return $this;
    }

    /**
     * Add Product to Compare Products List action
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return unknown
     */
    public function catalogProductCompareAddProduct(\Magento\Event\Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        $this->_productCompFactory
            ->create()
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(\Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE, $productId);
    }

    /**
     * Add product to shopping cart action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function checkoutCartAddProduct(\Magento\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem->getId() && !$quoteItem->getParentItem()) {
            $productId = $quoteItem->getProductId();
            $this->_event(\Magento\Reports\Model\Event::EVENT_PRODUCT_TO_CART, $productId);
        }
        return $this;
    }

    /**
     * Add product to wishlist action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function wishlistAddProduct(\Magento\Event\Observer $observer)
    {
        return $this->_event(\Magento\Reports\Model\Event::EVENT_PRODUCT_TO_WISHLIST,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Share customer wishlist action
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Reports\Model\Event\Observer
     */
    public function wishlistShare(\Magento\Event\Observer $observer)
    {
        return $this->_event(\Magento\Reports\Model\Event::EVENT_WISHLIST_SHARE,
            $observer->getEvent()->getWishlist()->getId()
        );
    }
}
