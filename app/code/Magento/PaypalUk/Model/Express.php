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
 * @package     Magento_PaypalUk
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPalUk Express Module
 */
namespace Magento\PaypalUk\Model;

class Express extends \Magento\Paypal\Model\Express
{
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;
    protected $_formBlockType = 'Magento\PaypalUk\Block\Express\Form';
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\PaypalUk\Model\Pro';

    /**
     * Express Checkout payment method instance
     *
     * @var \Magento\Paypal\Model\Express
     */
    protected $_ecInstance = null;

    /**
     * @var \Magento\Paypal\Model\InfoFactory
     */
    protected $_paypalInfoFactory;

    /**
     * @param \Magento\Paypal\Model\InfoFactory $paypalInfoFactory
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\Paypal\Model\CartFactory $cartFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Paypal\Model\InfoFactory $paypalInfoFactory,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\Paypal\Model\CartFactory $cartFactory,
        array $data = array()
    ) {
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $logAdapterFactory, $proTypeFactory,
            $storeManager, $urlBuilder, $cartFactory, $data);
        $this->_paypalInfoFactory = $paypalInfoFactory;
    }

    /**
     * EC PE won't be available if the EC is available
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = $this->_paymentData
                ->getMethodInstance(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
        }
        if ($quote && $this->_ecInstance) {
            $this->_ecInstance->setStore($quote->getStoreId());
        }
        return $this->_ecInstance ? !$this->_ecInstance->isAvailable() : false;
    }

    /**
     * Import payment info to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired() || $api->getRedirectRequested()
            )
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(\Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId())
        ;
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getTransactionId()));
        $this->_paypalInfoFactory->create()->importToPayment($api, $payment);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see \Magento\Sales\Model\Quote\Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('paypaluk/express/start');
    }
}
