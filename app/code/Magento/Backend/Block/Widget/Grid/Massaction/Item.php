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
 * @package     Magento_Backend
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Grid widget massaction single action item
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Massaction;

class Item extends \Magento\Backend\Block\Widget
{

    protected $_massaction = null;

    /**
     * Set parent massaction block
     *
     * @param  \Magento\Backend\Block\Widget\Grid\Massaction\Extended $massaction
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Item
     */
    public function setMassaction($massaction)
    {
        $this->_massaction = $massaction;
        return $this;
    }

    /**
     * Retrive parent massaction block
     *
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Extended
     */
    public function getMassaction()
    {
        return $this->_massaction;
    }

    /**
     * Set additional action block for this item
     *
     * @param string|\Magento\Core\Block\AbstractBlock $block
     * @return \Magento\Backend\Block\Widget\Grid\Massaction\Item
     * @throws \Magento\Core\Exception
     */
    public function setAdditionalActionBlock($block)
    {
        if (is_string($block)) {
            $block = $this->getLayout()->createBlock($block);
        } elseif (is_array($block)) {
            $block = $this->_createFromConfig($block);
        } elseif (!($block instanceof \Magento\Core\Block\AbstractBlock)) {
            throw new \Magento\Core\Exception('Unknown block type');
        }

        $this->setChild('additional_action', $block);
        return $this;
    }

    protected function _createFromConfig(array $config)
    {
        $type = isset($config['type']) ? $config['type'] : 'default';
        switch($type) {
            default:
                $blockClass = 'Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional\DefaultAdditional';
                break;
        }

        $block = $this->getLayout()->createBlock($blockClass);
        $block->createFromConfiguration(isset($config['type']) ? $config['config'] : $config);
        return $block;
    }

    /**
     * Retrive additional action block for this item
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function getAdditionalActionBlock()
    {
        return $this->getChildBlock('additional_action');
    }

    /**
     * Retrive additional action block HTML for this item
     *
     * @return string
     */
    public function getAdditionalActionBlockHtml()
    {
        return $this->getChildHtml('additional_action');
    }

}
