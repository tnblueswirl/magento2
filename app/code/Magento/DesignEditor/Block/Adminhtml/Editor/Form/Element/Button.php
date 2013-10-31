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
 * @package     Magento_DesignEditor
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form element button
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element;

class Button extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('data-mage-init');

    /**
     * Generate button html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        if ($this->getBeforeElementHtml()) {
            $html .= sprintf(
                '<label class="addbefore" for="%s">%s</label>', $this->getHtmlId(), $this->getBeforeElementHtml()
            );
        }
        $html .= sprintf(
            '<button id="%s" %s %s><span>%s</span></button>',
            $this->getHtmlId(),
            $this->_getUiId(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getEscapedValue()
        );

        if ($this->getAfterElementHtml()) {
            $html .= sprintf(
                '<label class="addafter" for="%s">%s</label>', $this->getHtmlId(), $this->getBeforeElementHtml()
            );
        }
        return $html;
    }

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
