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
 * @package     Magento_CurrencySymbol
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Currency Symbol Observer
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Model;

class Observer
{
    /**
     * Currency symbol data
     *
     * @var \Magento\CurrencySymbol\Helper\Data
     */
    protected $_currencySymbolData = null;

    /**
     * @param \Magento\CurrencySymbol\Helper\Data $currencySymbolData
     */
    public function __construct(
        \Magento\CurrencySymbol\Helper\Data $currencySymbolData
    ) {
        $this->_currencySymbolData = $currencySymbolData;
    }

    /**
     * Generate options for currency displaying with custom currency symbol
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CurrencySymbol\Model\Observer
     */
    public function currencyDisplayOptions(\Magento\Event\Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData($this->_currencySymbolData->getCurrencyOptions($baseCode));

        return $this;
    }
}
