<?php
/**
 * Google Optimizer Observer Category Tab
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
 * @copyright Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\GoogleOptimizer\Model\Observer\Block\Category;

class Tab
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\GoogleOptimizer\Helper\Data $helper,
        \Magento\View\LayoutInterface $layout
    ) {
        $this->_helper = $helper;
        $this->_layout = $layout;
    }

    /**
     * Adds Google Experiment tab to the category edit page
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addGoogleExperimentTab(\Magento\Event\Observer $observer)
    {
        if ($this->_helper->isGoogleExperimentActive()) {
            $block = $this->_layout->createBlock(
                'Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Category\Edit\Tab\Googleoptimizer',
                'google-experiment-form'
            );

            /** @var $tabs \Magento\Adminhtml\Block\Catalog\Category\Tabs */
            $tabs = $observer->getEvent()->getTabs();
            $tabs->addTab('google-experiment-tab', array(
                'label' => __('Category View Optimization'),
                'content' => $block->toHtml(),
            ));
        }
    }
}
