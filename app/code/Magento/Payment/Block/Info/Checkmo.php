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


namespace Magento\Payment\Block\Info;

class Checkmo extends \Magento\Payment\Block\Info
{

    protected $_payableTo;
    protected $_mailingAddress;

    protected $_template = 'Magento_Payment::info/checkmo.phtml';

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPayableTo()
    {
        if (is_null($this->_payableTo)) {
            $this->_convertAdditionalData();
        }
        return $this->_payableTo;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getMailingAddress()
    {
        if (is_null($this->_mailingAddress)) {
            $this->_convertAdditionalData();
        }
        return $this->_mailingAddress;
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Payment\Block\Info\Checkmo
     */
    protected function _convertAdditionalData()
    {
        $details = @unserialize($this->getInfo()->getAdditionalData());
        if (is_array($details)) {
            $this->_payableTo = isset($details['payable_to']) ? (string) $details['payable_to'] : '';
            $this->_mailingAddress = isset($details['mailing_address']) ? (string) $details['mailing_address'] : '';
        } else {
            $this->_payableTo = '';
            $this->_mailingAddress = '';
        }
        return $this;
    }

    public function toPdf()
    {
        $this->setTemplate('Magento_Payment::info/pdf/checkmo.phtml');
        return $this->toHtml();
    }

}
