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
 * @method \Magento\Sales\Model\Resource\Order\Invoice _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Invoice getResource()
 * @method int getStoreId()
 * @method \Magento\Sales\Model\Order\Invoice setStoreId(int $value)
 * @method float getBaseGrandTotal()
 * @method \Magento\Sales\Model\Order\Invoice setBaseGrandTotal(float $value)
 * @method float getShippingTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setShippingTaxAmount(float $value)
 * @method float getTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setTaxAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setBaseTaxAmount(float $value)
 * @method float getStoreToOrderRate()
 * @method \Magento\Sales\Model\Order\Invoice setStoreToOrderRate(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setBaseShippingTaxAmount(float $value)
 * @method float getBaseDiscountAmount()
 * @method \Magento\Sales\Model\Order\Invoice setBaseDiscountAmount(float $value)
 * @method float getBaseToOrderRate()
 * @method \Magento\Sales\Model\Order\Invoice setBaseToOrderRate(float $value)
 * @method float getGrandTotal()
 * @method \Magento\Sales\Model\Order\Invoice setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method \Magento\Sales\Model\Order\Invoice setShippingAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method \Magento\Sales\Model\Order\Invoice setSubtotalInclTax(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method \Magento\Sales\Model\Order\Invoice setBaseSubtotalInclTax(float $value)
 * @method float getStoreToBaseRate()
 * @method \Magento\Sales\Model\Order\Invoice setStoreToBaseRate(float $value)
 * @method float getBaseShippingAmount()
 * @method \Magento\Sales\Model\Order\Invoice setBaseShippingAmount(float $value)
 * @method float getTotalQty()
 * @method \Magento\Sales\Model\Order\Invoice setTotalQty(float $value)
 * @method float getBaseToGlobalRate()
 * @method \Magento\Sales\Model\Order\Invoice setBaseToGlobalRate(float $value)
 * @method float getSubtotal()
 * @method \Magento\Sales\Model\Order\Invoice setSubtotal(float $value)
 * @method float getBaseSubtotal()
 * @method \Magento\Sales\Model\Order\Invoice setBaseSubtotal(float $value)
 * @method float getDiscountAmount()
 * @method \Magento\Sales\Model\Order\Invoice setDiscountAmount(float $value)
 * @method int getBillingAddressId()
 * @method \Magento\Sales\Model\Order\Invoice setBillingAddressId(int $value)
 * @method int getIsUsedForRefund()
 * @method \Magento\Sales\Model\Order\Invoice setIsUsedForRefund(int $value)
 * @method int getOrderId()
 * @method \Magento\Sales\Model\Order\Invoice setOrderId(int $value)
 * @method int getEmailSent()
 * @method \Magento\Sales\Model\Order\Invoice setEmailSent(int $value)
 * @method int getCanVoidFlag()
 * @method \Magento\Sales\Model\Order\Invoice setCanVoidFlag(int $value)
 * @method int getState()
 * @method \Magento\Sales\Model\Order\Invoice setState(int $value)
 * @method int getShippingAddressId()
 * @method \Magento\Sales\Model\Order\Invoice setShippingAddressId(int $value)
 * @method string getStoreCurrencyCode()
 * @method \Magento\Sales\Model\Order\Invoice setStoreCurrencyCode(string $value)
 * @method string getTransactionId()
 * @method \Magento\Sales\Model\Order\Invoice setTransactionId(string $value)
 * @method string getOrderCurrencyCode()
 * @method \Magento\Sales\Model\Order\Invoice setOrderCurrencyCode(string $value)
 * @method string getBaseCurrencyCode()
 * @method \Magento\Sales\Model\Order\Invoice setBaseCurrencyCode(string $value)
 * @method string getGlobalCurrencyCode()
 * @method \Magento\Sales\Model\Order\Invoice setGlobalCurrencyCode(string $value)
 * @method string getIncrementId()
 * @method \Magento\Sales\Model\Order\Invoice setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Invoice setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Order\Invoice setUpdatedAt(string $value)
 * @method float getHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmnt()
 * @method \Magento\Sales\Model\Order\Invoice setBaseShippingHiddenTaxAmnt(float $value)
 * @method float getShippingInclTax()
 * @method \Magento\Sales\Model\Order\Invoice setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method \Magento\Sales\Model\Order\Invoice setBaseShippingInclTax(float $value)
 */
namespace Magento\Sales\Model\Order;

class Invoice extends \Magento\Sales\Model\AbstractModel
{
    /**
     * Invoice states
     */
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;

    const CAPTURE_ONLINE   = 'online';
    const CAPTURE_OFFLINE  = 'offline';
    const NOT_CAPTURE      = 'not_capture';

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/invoice/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/invoice/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/invoice/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/invoice/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/invoice/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/invoice/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/invoice_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/invoice_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/invoice_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/invoice_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/invoice_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/invoice_comment/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_INVOICE_CREATED      = 'invoice_created';

    /*
     * Identifier for order history item
     */
    const HISTORY_ENTITY_NAME = 'invoice';

    protected static $_states;

    protected $_items;
    protected $_comments;
    protected $_order;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_rounders = array();

    protected $_saveBeforeDestruct = false;

    protected $_eventPrefix = 'sales_order_invoice';
    protected $_eventObject = 'invoice';

    /**
     * Whether the pay() was called
     * @var bool
     */
    protected $_wasPayCalled = false;

    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData;

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Config
     */
    protected $_invoiceConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Sales\Model\Resource\OrderFactory
     */
    protected $_orderResourceFactory;

    /**
     * @var \Magento\Core\Model\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory
     */
    protected $_invoiceItemCollFactory;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\CommentFactory
     */
    protected $_invoiceCommentFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Invoice\Comment\CollectionFactory
     */
    protected $_commentCollFactory;

    /**
     * @var \Magento\Core\Model\Email\Template\MailerFactory
     */
    protected $_templateMailerFactory;

    /**
     * @var \Magento\Core\Model\Email\InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\LocaleInterface $coreLocale
     * @param \Magento\Sales\Model\Order\Invoice\Config $invoiceConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Resource\OrderFactory $orderResourceFactory
     * @param \Magento\Core\Model\CalculatorFactory $calculatorFactory
     * @param \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $invoiceItemCollFactory
     * @param \Magento\Sales\Model\Order\Invoice\CommentFactory $invoiceCommentFactory
     * @param \Magento\Sales\Model\Resource\Order\Invoice\Comment\CollectionFactory $commentCollFactory
     * @param \Magento\Core\Model\Email\Template\MailerFactory $templateMailerFactory
     * @param \Magento\Core\Model\Email\InfoFactory $emailInfoFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Core\Model\LocaleInterface $coreLocale,
        \Magento\Sales\Model\Order\Invoice\Config $invoiceConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Resource\OrderFactory $orderResourceFactory,
        \Magento\Core\Model\CalculatorFactory $calculatorFactory,
        \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $invoiceItemCollFactory,
        \Magento\Sales\Model\Order\Invoice\CommentFactory $invoiceCommentFactory,
        \Magento\Sales\Model\Resource\Order\Invoice\Comment\CollectionFactory $commentCollFactory,
        \Magento\Core\Model\Email\Template\MailerFactory $templateMailerFactory,
        \Magento\Core\Model\Email\InfoFactory $emailInfoFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_paymentData = $paymentData;
        $this->_salesData = $salesData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_invoiceConfig = $invoiceConfig;
        $this->_orderFactory = $orderFactory;
        $this->_orderResourceFactory = $orderResourceFactory;
        $this->_calculatorFactory = $calculatorFactory;
        $this->_invoiceItemCollFactory = $invoiceItemCollFactory;
        $this->_invoiceCommentFactory = $invoiceCommentFactory;
        $this->_commentCollFactory = $commentCollFactory;
        $this->_templateMailerFactory = $templateMailerFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        parent::__construct($context, $registry, $coreLocale, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize invoice resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Invoice');
    }

    /**
     * Load invoice by increment id
     *
     * @param string $incrementId
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function loadByIncrementId($incrementId)
    {
        $ids = $this->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();

        if (!empty($ids)) {
            reset($ids);
            $this->load(current($ids));
        }
        return $this;
    }

    /**
     * Retrieve invoice configuration model
     *
     * @return \Magento\Sales\Model\Order\Invoice\Config
     */
    public function getConfig()
    {
        return $this->_invoiceConfig;
    }

    /**
     * Retrieve store model instance
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for invoice
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Sales\Model\Order\Invoice
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the invoice for created for
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof \Magento\Sales\Model\Order) {
            $this->_order = $this->_orderFactory->create()->load($this->getOrderId());
        }
        return $this->_order->setHistoryEntityName(self::HISTORY_ENTITY_NAME);
    }

    /**
     * Retrieve the increment_id of the order
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->_orderResourceFactory->create()->getIncrementId($this->getOrderId());
    }

    /**
     * Retrieve billing address
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * Check invoice cancel state
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->getState() == self::STATE_CANCELED;
    }

    /**
     * Check invice capture action availability
     *
     * @return bool
     */
    public function canCapture()
    {
        return $this->getState() != self::STATE_CANCELED
            && $this->getState() != self::STATE_PAID
            && $this->getOrder()->getPayment()->canCapture();
    }

    /**
     * Check invoice void action availability
     *
     * @return bool
     */
    public function canVoid()
    {
        if ($this->getState() == self::STATE_PAID) {
            if (is_null($this->getCanVoidFlag())) {
                return (bool)$this->getOrder()->getPayment()->canVoid($this);
            }
        }
        return (bool)$this->getCanVoidFlag();
    }

    /**
     * Check invoice cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check invoice refund action availability
     *
     * @return bool
     */
    public function canRefund()
    {
        if ($this->getState() != self::STATE_PAID) {
            return false;
        }
        if (abs($this->getBaseGrandTotal() - $this->getBaseTotalRefunded()) < .0001) {
            return false;
        }
        return true;
    }

    /**
     * Capture invoice
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function capture()
    {
        $this->getOrder()->getPayment()->capture($this);
        if ($this->getIsPaid()) {
            $this->pay();
        }
        return $this;
    }

    /**
     * Pay invoice
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function pay()
    {
        if ($this->_wasPayCalled) {
            return $this;
        }
        $this->_wasPayCalled = true;

        $invoiceState = self::STATE_PAID;
        if ($this->getOrder()->getPayment()->hasForcedState()) {
            $invoiceState = $this->getOrder()->getPayment()->getForcedState();
        }

        $this->setState($invoiceState);

        $this->getOrder()->getPayment()->pay($this);
        $this->getOrder()->setTotalPaid(
            $this->getOrder()->getTotalPaid()+$this->getGrandTotal()
        );
        $this->getOrder()->setBaseTotalPaid(
            $this->getOrder()->getBaseTotalPaid()+$this->getBaseGrandTotal()
        );
        $this->_eventManager->dispatch('sales_order_invoice_pay', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Whether pay() method was called (whether order and payment totals were updated)
     * @return bool
     */
    public function wasPayCalled()
    {
        return $this->_wasPayCalled;
    }

    /**
     * Void invoice
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function void()
    {
        $this->getOrder()->getPayment()->void($this);
        $this->cancel();
        return $this;
    }

    /**
     * Cancel invoice action
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function cancel()
    {
        $order = $this->getOrder();
        $order->getPayment()->cancelInvoice($this);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }

        /**
         * Unregister order totals only for invoices in state PAID
         */
        $order->setTotalInvoiced($order->getTotalInvoiced() - $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() - $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() - $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() - $this->getBaseSubtotal());

        $order->setTaxInvoiced($order->getTaxInvoiced() - $this->getTaxAmount());
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() - $this->getBaseTaxAmount());

        $order->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced() - $this->getHiddenTaxAmount());
        $order->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced() - $this->getBaseHiddenTaxAmount());

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() - $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() - $this->getBaseShippingTaxAmount());

        $order->setShippingInvoiced($order->getShippingInvoiced() - $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() - $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() - $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() - $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() - $this->getBaseCost());


        if ($this->getState() == self::STATE_PAID) {
            $this->getOrder()->setTotalPaid($this->getOrder()->getTotalPaid()-$this->getGrandTotal());
            $this->getOrder()->setBaseTotalPaid($this->getOrder()->getBaseTotalPaid()-$this->getBaseGrandTotal());
        }
        $this->setState(self::STATE_CANCELED);
        $this->getOrder()->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
        $this->_eventManager->dispatch('sales_order_invoice_cancel', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Invoice totals collecting
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function collectTotals()
    {
        foreach ($this->getConfig()->getTotalModels() as $model) {
            $model->collect($this);
        }
        return $this;
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_rounders[$type])) {
                $this->_rounders[$type] = $this->_calculatorFactory->create(array('store' => $this->getStore()));
            }
            $price = $this->_rounders[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * Get invoice items collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Invoice\Item\Collection
     */
    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = $this->_invoiceItemCollFactory->create()->setInvoiceFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setInvoice($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * @return array
     */
    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * @param int|string $itemId
     * @return bool|\Magento\Sales\Model\Order\Invoice\Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice\Item $item
     * @return $this
     */
    public function addItem(\Magento\Sales\Model\Order\Invoice\Item $item)
    {
        $item->setInvoice($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());

        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Retrieve invoice states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (null === self::$_states) {
            self::$_states = array(
                self::STATE_OPEN       => __('Pending'),
                self::STATE_PAID       => __('Paid'),
                self::STATE_CANCELED   => __('Canceled'),
            );
        }
        return self::$_states;
    }

    /**
     * Retrieve invoice state name by state identifier
     *
     * @param   int|null $stateId
     * @return  string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (null === self::$_states) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return __('Unknown State');
    }

    /**
     * Register invoice
     *
     * Apply to order, order items etc.
     *
     * @return $this
     * @throws \Magento\Core\Exception
     */
    public function register()
    {
        if ($this->getId()) {
            throw new \Magento\Core\Exception(__('We cannot register an existing invoice'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();
            } else {
                $item->isDeleted(true);
            }
        }

        $order = $this->getOrder();
        $captureCase = $this->getRequestedCaptureCase();
        if ($this->canCapture()) {
            if ($captureCase) {
                if ($captureCase == self::CAPTURE_ONLINE) {
                    $this->capture();
                } elseif ($captureCase == self::CAPTURE_OFFLINE) {
                    $this->setCanVoidFlag(false);
                    $this->pay();
                }
            }
        } elseif (!$order->getPayment()->getMethodInstance()->isGateway() || $captureCase == self::CAPTURE_OFFLINE) {
            if (!$order->getPayment()->getIsTransactionPending()) {
                $this->setCanVoidFlag(false);
                $this->pay();
            }
        }

        $order->setTotalInvoiced($order->getTotalInvoiced() + $this->getGrandTotal());
        $order->setBaseTotalInvoiced($order->getBaseTotalInvoiced() + $this->getBaseGrandTotal());

        $order->setSubtotalInvoiced($order->getSubtotalInvoiced() + $this->getSubtotal());
        $order->setBaseSubtotalInvoiced($order->getBaseSubtotalInvoiced() + $this->getBaseSubtotal());

        $order->setTaxInvoiced($order->getTaxInvoiced() + $this->getTaxAmount());
        $order->setBaseTaxInvoiced($order->getBaseTaxInvoiced() + $this->getBaseTaxAmount());

        $order->setHiddenTaxInvoiced($order->getHiddenTaxInvoiced() + $this->getHiddenTaxAmount());
        $order->setBaseHiddenTaxInvoiced($order->getBaseHiddenTaxInvoiced() + $this->getBaseHiddenTaxAmount());

        $order->setShippingTaxInvoiced($order->getShippingTaxInvoiced() + $this->getShippingTaxAmount());
        $order->setBaseShippingTaxInvoiced($order->getBaseShippingTaxInvoiced() + $this->getBaseShippingTaxAmount());


        $order->setShippingInvoiced($order->getShippingInvoiced() + $this->getShippingAmount());
        $order->setBaseShippingInvoiced($order->getBaseShippingInvoiced() + $this->getBaseShippingAmount());

        $order->setDiscountInvoiced($order->getDiscountInvoiced() + $this->getDiscountAmount());
        $order->setBaseDiscountInvoiced($order->getBaseDiscountInvoiced() + $this->getBaseDiscountAmount());
        $order->setBaseTotalInvoicedCost($order->getBaseTotalInvoicedCost() + $this->getBaseCost());

        $state = $this->getState();
        if (null === $state) {
            $this->setState(self::STATE_OPEN);
        }

        $this->_eventManager->dispatch('sales_order_invoice_register', array(
            $this->_eventObject => $this,
            'order' => $order
        ));
        return $this;
    }

    /**
     * Checking if the invoice is last
     *
     * @return bool
     */
    public function isLast()
    {
        foreach ($this->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds comment to invoice with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param string $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!($comment instanceof \Magento\Sales\Model\Order\Invoice\Comment)) {
            $comment = $this->_invoiceCommentFactory->create()
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setInvoice($this)
            ->setStoreId($this->getStoreId())
            ->setParentId($this->getId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = true;
        return $this;
    }

    /**
     * @param bool $reload
     * @return \Magento\Sales\Model\Resource\Order\Invoice\Comment\Collection
     */
    public function getCommentsCollection($reload=false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = $this->_commentCollFactory->create()
                ->setInvoiceFilter($this->getId())
                ->setCreatedAtOrder();
            /**
             * When invoice created with adding comment, comments collection
             * must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setInvoice($this);
                }
            }
        }
        return $this->_comments;
    }

    /**
     * Send email with invoice data
     *
     * @param bool $notifyCustomer
     * @param string $comment
     * @return \Magento\Sales\Model\Order\Invoice
     * @throws \Exception
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->_salesData->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        $paymentBlockHtml = $this->_paymentData->getInfoBlockHtml($order->getPayment(), $storeId);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = $this->_templateMailerFactory->create();
        if ($notifyCustomer) {
            $emailInfo = $this->_emailInfoFactory->create();
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = $this->_emailInfoFactory->create();
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender($this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
            'order'        => $order,
            'invoice'      => $this,
            'comment'      => $comment,
            'billing'      => $order->getBillingAddress(),
            'payment_html' => $paymentBlockHtml
        ));
        $mailer->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with invoice update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->_salesData->canSendInvoiceCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = $this->_templateMailerFactory->create();
        if ($notifyCustomer) {
            $emailInfo = $this->_emailInfoFactory->create();
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = $this->_emailInfoFactory->create();
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender($this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'invoice'      => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }

    /**
     * @param $configPath
     * @return array|bool
     */
    protected function _getEmails($configPath)
    {
        $data = $this->_coreStoreConfig->getConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Reset invoice object
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function reset()
    {
        $this->unsetData();
        $this->_origData = null;
        $this->_items = null;
        $this->_comments = null;
        $this->_order = null;
        $this->_saveBeforeDestruct = false;
        $this->_wasPayCalled = false;
        return $this;
    }

    /**
     * Before object save manipulations
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
            $this->setBillingAddressId($this->getOrder()->getBillingAddress()->getId());
        }

        return $this;
    }

    /**
     * After object save manipulation
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    protected function _afterSave()
    {

        if (null !== $this->_items) {
            /**
             * Save invoice items
             */
            foreach ($this->_items as $item) {
                $item->setOrderItem($item->getOrderItem());
                $item->save();
            }
        }

        if (null !== $this->_comments) {
            foreach ($this->_comments as $comment) {
                $comment->save();
            }
        }

        return parent::_afterSave();
    }
}
