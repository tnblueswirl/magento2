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
 * Button widget
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme;

class Button extends \Magento\Core\Block\Template
{
    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Magento_DesignEditor::theme/button.phtml');
        parent::_construct();
    }

    /**
     * Retrieve attributes html
     *
     * @return string
     */
    public function getAttributesHtml()
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $title = $this->getTitle() ?: $this->getLabel();

        $classes = array();
        if ($this->getClass()) {
            $classes[] = $this->getClass();
        }
        if ($disabled) {
            $classes[] = $disabled;
        }

        return $this->_attributesToHtml($this->_prepareAttributes($title, $classes, $disabled));
    }

    /**
     * Prepare attributes
     *
     * @param string $title
     * @param array $classes
     * @param string $disabled
     * @return array
     */
    protected function _prepareAttributes($title, $classes, $disabled)
    {
        return array(
            'id'        => $this->getId(),
            'name'      => $this->getElementName(),
            'href'      => $this->getHref(),
            'title'     => $title,
            'class'     => implode(' ', $classes),
            'style'     => $this->getStyle(),
            'target'    => $this->getTarget(),
            'disabled'  => $disabled
        );
    }

    /**
     * Attributes list to html
     *
     * @param array $attributes
     * @return string
     */
    protected function _attributesToHtml($attributes)
    {
        $helper = $this->helper('Magento\Backend\Helper\Data');
        $html = '';
        foreach ($attributes as $attributeKey => $attributeValue) {
            if ($attributeValue !== null && $attributeValue !== '') {
                $html .= $attributeKey . '="' . $helper->escapeHtml($attributeValue) . '" ';
            }
        }
        return $html;
    }
}
