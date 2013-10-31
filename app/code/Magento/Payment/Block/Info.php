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
 * Base payment iformation block
 */
namespace Magento\Payment\Block;

class Info extends \Magento\Core\Block\Template
{
    /**
     * Payment rendered specific information
     *
     * @var \Magento\Object
     */
    protected $_paymentSpecificInformation = null;

    protected $_template = 'Magento_Payment::info/default.phtml';

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve info model
     *
     * @return \Magento\Payment\Model\Info
     * @throws \Magento\Core\Exception
     */
    public function getInfo()
    {
        $info = $this->getData('info');
        if (!($info instanceof \Magento\Payment\Model\Info)) {
            throw new \Magento\Core\Exception(__('We cannot retrieve the payment info model object.'));
        }
        return $info;
    }

    /**
     * Retrieve payment method model
     *
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function getMethod()
    {
        return $this->getInfo()->getMethodInstance();
    }

    /**
     * Render as PDF
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_Payment::info/pdf/default.phtml');
        return $this->toHtml();
    }

    /**
     * Getter for children PDF, as array. Analogue of $this->getChildHtml()
     *
     * Children must have toPdf() callable
     * Known issue: not sorted
     * @return array
     */
    public function getChildPdfAsArray()
    {
        $result = array();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if (method_exists($child, 'toPdf')) {
                $result[] = $child->toPdf();
            }
        }
        return $result;
    }

    /**
     * Get some specific information in format of array($label => $value)
     *
     * @return array
     */
    public function getSpecificInformation()
    {
        return $this->_prepareSpecificInformation()->getData();
    }

    /**
     * Render the value as an array
     *
     * @param mixed $value
     * @param bool $escapeHtml
     * @return array
     */
    public function getValueAsArray($value, $escapeHtml = false)
    {
        if (empty($value)) {
            return array();
        }
        if (!is_array($value)) {
            $value = array($value);
        }
        if ($escapeHtml) {
            foreach ($value as $_key => $_val) {
                $value[$_key] = $this->escapeHtml($_val);
            }
        }
        return $value;
    }

    /**
     * Check whether payment information should show up in secure mode
     * true => only "public" payment information may be shown
     * false => full information may be shown
     *
     * @return bool
     */
    public function getIsSecureMode()
    {
        if ($this->hasIsSecureMode()) {
            return (bool)(int)$this->_getData('is_secure_mode');
        }
        if (!$payment = $this->getInfo()) {
            return true;
        }
        if (!$method = $payment->getMethodInstance()) {
            return true;
        }
        return !$this->_storeManager->getStore($method->getStore())->isAdmin();
    }

    /**
     * Prepare information specific to current payment method
     *
     * @param \Magento\Object|array $transport
     * @return \Magento\Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null === $this->_paymentSpecificInformation) {
            if (null === $transport) {
                $transport = new \Magento\Object;
            } elseif (is_array($transport)) {
                $transport = new \Magento\Object($transport);
            }
            $this->_eventManager->dispatch('payment_info_block_prepare_specific_information', array(
                'transport' => $transport,
                'payment'   => $this->getInfo(),
                'block'     => $this,
            ));
            $this->_paymentSpecificInformation = $transport;
        }
        return $this->_paymentSpecificInformation;
    }
}
