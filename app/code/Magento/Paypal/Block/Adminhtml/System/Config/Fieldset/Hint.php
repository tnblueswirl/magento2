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
 * @package     Magento_Paypal
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Renderer for PayPal banner in System Configuration
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset;

class Hint
    extends \Magento\Backend\Block\Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Magento_Paypal::system/config/fieldset/hint.phtml';

    /**
     * Render fieldset html
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_link'])) {
            $this->setHelpLink($elementOriginalData['help_link']);
        }
        $js = '
            paypalToggleSolution = function(id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if ($(this).hasClassName("open")) {
                    $$(".with-button button.button").each(function(anotherButton) {
                        if (anotherButton != this && $(anotherButton).hasClassName("open")) {
                            $(anotherButton).click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var pos = Element.cumulativeOffset($(this));
                    window.scrollTo(pos[0], pos[1] - 45);
                }
            }

            togglePaypalSolutionConfigureButton = function(button, enable) {
                var $button = $(button);
                $button.disabled = !enable;
                if ($button.hasClassName("disabled") && enable) {
                    $button.removeClassName("disabled");
                } else if (!$button.hasClassName("disabled") && !enable) {
                    $button.addClassName("disabled");
                }
            }

            // check store-view disabling Express Checkout
            document.observe("dom:loaded", function() {
                $$(".pp-method-express button.button").each(function(ecButton){
                    var ecEnabler = $$(".paypal-ec-enabler")[0];
                    if (typeof ecButton == "undefined" || typeof ecEnabler != "undefined") {
                        return;
                    }
                    var $ecButton = $(ecButton);
                    $$(".with-button button.button").each(function(configureButton) {
                        if (configureButton != ecButton && !configureButton.disabled
                            && !$(configureButton).hasClassName("paypal-ec-separate")
                        ) {
                            togglePaypalSolutionConfigureButton(ecButton, false);
                        }
                    });
                });
            });
        ';
        return $this->toHtml() . $this->helper('Magento\Adminhtml\Helper\Js')->getScript($js);
    }
}
