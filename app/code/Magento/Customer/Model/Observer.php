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
 * @package     Magento_Customer
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer module observer
 */
namespace Magento\Customer\Model;

class Observer
{
    /**
     * VAT ID validation processed flag code
     */
    const VIV_PROCESSED_FLAG = 'viv_after_address_save_processed';

    /**
     * VAT ID validation currently saved address flag
     */
    const VIV_CURRENTLY_SAVED_ADDRESS = 'currently_saved_address';

    /**
     * Customer address
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreData = $coreData;
        $this->_customerData = $customerData;
        $this->_customerAddress = $customerAddress;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * Check whether specified billing address is default for its customer
     *
     * @param \Magento\Customer\Model\Address $address
     * @return bool
     */
    protected function _isDefaultBilling($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultBilling())
            || $address->getIsPrimaryBilling() || $address->getIsDefaultBilling();
    }

    /**
     * Check whether specified shipping address is default for its customer
     *
     * @param \Magento\Customer\Model\Address $address
     * @return bool
     */
    protected function _isDefaultShipping($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultShipping())
            || $address->getIsPrimaryShipping() || $address->getIsDefaultShipping();
    }

    /**
     * Check whether specified address should be processed in after_save event handler
     *
     * @param \Magento\Customer\Model\Address $address
     * @return bool
     */
    protected function _canProcessAddress($address)
    {
        if ($address->getForceProcess()) {
            return true;
        }

        if ($this->_coreRegistry->registry(self::VIV_CURRENTLY_SAVED_ADDRESS) != $address->getId()) {
            return false;
        }

        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType();
        if ($configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING) {
            return $this->_isDefaultShipping($address);
        }
        return $this->_isDefaultBilling($address);
    }

    /**
     * Address before save event handler
     *
     * @param \Magento\Event\Observer $observer
     */
    public function beforeAddressSave($observer)
    {
        if ($this->_coreRegistry->registry(self::VIV_CURRENTLY_SAVED_ADDRESS)) {
            $this->_coreRegistry->unregister(self::VIV_CURRENTLY_SAVED_ADDRESS);
        }

        /** @var $customerAddress \Magento\Customer\Model\Address */
        $customerAddress = $observer->getCustomerAddress();
        if ($customerAddress->getId()) {
            $this->_coreRegistry->register(self::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddress->getId());
        } else {
            $configAddressType = $this->_customerAddress->getTaxCalculationAddressType();

            $forceProcess = ($configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING)
                ? $customerAddress->getIsDefaultShipping() : $customerAddress->getIsDefaultBilling();

            if ($forceProcess) {
                $customerAddress->setForceProcess(true);
            } else {
                $this->_coreRegistry->register(self::VIV_CURRENTLY_SAVED_ADDRESS, 'new_address');
            }
        }
    }

    /**
     * Address after save event handler
     *
     * @param \Magento\Event\Observer $observer
     */
    public function afterAddressSave($observer)
    {
        /** @var $customerAddress \Magento\Customer\Model\Address */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if (!$this->_customerAddress->isVatValidationEnabled($customer->getStore())
            || $this->_coreRegistry->registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, true);

            /** @var $customerHelper \Magento\Customer\Helper\Data */
            $customerHelper = $this->_customerData;

            if ($customerAddress->getVatId() == ''
                || !$this->_coreData->isCountryInEU($customerAddress->getCountry()))
            {
                $defaultGroupId = $customerHelper->getDefaultCustomerGroupId($customer->getStore());

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customer->setGroupId($defaultGroupId);
                    $customer->save();
                }
            } else {
                $result = $customerHelper->checkVatNumber(
                    $customerAddress->getCountryId(),
                    $customerAddress->getVatId()
                );

                $newGroupId = $customerHelper->getCustomerGroupIdBasedOnVatNumber(
                    $customerAddress->getCountryId(), $result, $customer->getStore()
                );

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $newGroupId) {
                    $customer->setGroupId($newGroupId);
                    $customer->save();
                }

                if (!$this->_storeManager->getStore()->isAdmin()) {
                    $validationMessage = $this->_customerData->getVatValidationUserMessage($customerAddress,
                        $customer->getDisableAutoGroupChange(), $result);

                    if (!$validationMessage->getIsError()) {
                        $this->_customerSession->addSuccess($validationMessage->getMessage());
                    } else {
                        $this->_customerSession->addError($validationMessage->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }

    /**
     * Revert emulated customer group_id
     *
     * @param \Magento\Event\Observer $observer
     */
    public function quoteSubmitAfter($observer)
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getQuote()->getCustomer();

        if (!$this->_customerAddress->isVatValidationEnabled($customer->getStore())) {
            return;
        }

        if (!$customer->getId()) {
            return;
        }

        $customer->setGroupId(
            (int)$customer->getOrigData('group_id')
        );
        $customer->save();
    }
}
