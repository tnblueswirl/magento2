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
 * Product alerts tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab;

class Alerts extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'catalog/product/tab/alert.phtml';

    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Accordion')
            ->setId('productAlerts');
        /* @var $accordion \Magento\Adminhtml\Block\Widget\Accordion */

        $alertPriceAllow = $this->_storeConfig->getConfig('catalog/productalert/allow_price');
        $alertStockAllow = $this->_storeConfig->getConfig('catalog/productalert/allow_stock');

        if ($alertPriceAllow) {
            $accordion->addItem('price', array(
                'title'     => __('We saved the price alert subscription.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Alerts\Price')
                    ->toHtml() . '<br />',
                'open'      => true
            ));
        }
        if ($alertStockAllow) {
            $accordion->addItem('stock', array(
                'title'     => __('We saved the stock notification.'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Alerts\Stock'),
                'open'      => true
            ));
        }

        $this->setChild('accordion', $accordion);

        return parent::_prepareLayout();
    }

    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }
}
