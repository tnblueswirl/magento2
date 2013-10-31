<?php
/**
 * Container for editing subscription grid
 *
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
 * @package     Magento_Webhook
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Webhook\Block\Adminhtml\Subscription;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Keys used to retrieve values from subscription data array */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';

    /** @var array $_subscriptionData */
    protected $_subscriptionData;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_objectId = 'id';
        $this->_blockGroup = 'Magento_Webhook';
        $this->_controller = 'adminhtml_subscription';
        $this->_subscriptionData = $registry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);
        // Don't allow the merchant to delete subscriptions that were generated by config file.
        if ($this->_isCreatedByConfig()) {
            $this->_removeButton('delete');
            $this->_removeButton('reset');
            $this->_removeButton('save');
        }
    }

    /**
     * Gets header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_isExistingSubscription()) {
            return __('Edit Subscription');
        } else {
            return __('Add Subscription');
        }
    }

    /**
     * Returns true is subscription exists
     *
     * @return bool
     */
    protected function _isExistingSubscription()
    {
        return $this->_subscriptionData
            && isset($this->_subscriptionData[self::DATA_SUBSCRIPTION_ID])
            && $this->_subscriptionData[self::DATA_SUBSCRIPTION_ID];
    }

    /**
     * Check whether subscription was generated from configuration.
     *
     * Return false if subscription created within UI.
     *
     * @return bool
     */
    protected function _isCreatedByConfig()
    {
        return $this->_subscriptionData && isset($this->_subscriptionData['alias']);
    }
}
