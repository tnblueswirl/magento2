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
 * @package     Magento_Checkout
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout choose item addresses block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class Addresses extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Filter\Object\GridFactory
     */
    protected $_filterGridFactory;

    /**
     * @var \Magento\Checkout\Model\Type\Multishipping
     */
    protected $_multishipping;

    /**
     * @param \Magento\Filter\Object\GridFactory $filterGridFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\Filter\Object\GridFactory $filterGridFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_filterGridFactory = $filterGridFactory;
        $this->_multishipping = $multishipping;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Ship to Multiple Addresses') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        /** @var \Magento\Filter\Object\Grid $itemsFilter */
        $itemsFilter = $this->_filterGridFactory->create();
        $itemsFilter->addFilter(new \Magento\Filter\Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param $item
     * @param int $index
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setId('ship_'.$index.'_'.$item->getQuoteItemId().'_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions());

        return $select->getHtml();
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/' . $item->getProductId());
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', array('address' => $item->getQuoteAddressId(), 'id' => $item->getId()));
    }

    /**
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }

    /**
     * @return string
     */
    public function getNewAddressUrl()
    {
        return $this->getUrl('*/multishipping_address/newShipping');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/cart/');
    }

    /**
     * @return bool
     */
    public function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
