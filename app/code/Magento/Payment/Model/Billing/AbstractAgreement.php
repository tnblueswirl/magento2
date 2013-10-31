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
 * @package     Magento_Payment
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Billing Agreement abstaract class
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Payment\Model\Billing;

abstract class AbstractAgreement extends \Magento\Core\Model\AbstractModel
{
    /**
     * Payment method instance
     *
     * @var \Magento\Payment\Model\Method\AbstractMethod
     */
    protected $_paymentMethodInstance = null;

    /**
     * Billing Agreement Errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Init billing agreement
     *
     */
    abstract public function initToken();

    /**
     * Verify billing agreement details
     *
     */
    abstract public function verifyToken();

    /**
     * Create billing agreement
     *
     */
    abstract public function place();

    /**
     * Cancel billing agreement
     *
     */
    abstract public function cancel();

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retreive payment method instance
     *
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function getPaymentMethodInstance()
    {
        if (is_null($this->_paymentMethodInstance)) {
            $this->_paymentMethodInstance = $this->_paymentData->getMethodInstance($this->getMethodCode());
        }
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($this->getStoreId());
        }
        return $this->_paymentMethodInstance;
    }

    /**
     * Validate data before save
     *
     * @return bool
     */
    public function isValid()
    {
        $this->_errors = array();
        if (is_null($this->getPaymentMethodInstance()) || !$this->getPaymentMethodInstance()->getCode()) {
            $this->_errors[] = __('The payment method code is not set.');
        }
        if (!$this->getReferenceId()) {
            $this->_errors[] = __('The reference ID is not set.');
        }
        return empty($this->_errors);
    }

    /**
     * Before save, it's overriden just to make data validation on before save event
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if ($this->isValid()) {
            return parent::_beforeSave();
        }
        array_unshift($this->_errors, __('Unable to save Billing Agreement:'));
        throw new \Magento\Core\Exception(implode(' ', $this->_errors));
    }
}
