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
 * @package     Magento_Core
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Theme Observer model
 */
namespace Magento\Core\Model\Theme;

class Observer
{
    /**
     * @var \Magento\Core\Model\Theme\ImageFactory
     */
    protected $_themeImageFactory;

    /**
     * @var \Magento\Core\Model\Resource\Layout\Update\Collection
     */
    protected $_updateCollection;

    /**
     * @var \Magento\Theme\Model\Config\Customization
     */
    protected $_themeConfig;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * @param \Magento\Core\Model\Theme\ImageFactory $themeImageFactory
     * @param \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection
     * @param \Magento\Theme\Model\Config\Customization $themeConfig
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Magento\Core\Model\Theme\ImageFactory $themeImageFactory,
        \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection,
        \Magento\Theme\Model\Config\Customization $themeConfig,
        \Magento\Event\ManagerInterface $eventDispatcher
    ) {
        $this->_themeImageFactory = $themeImageFactory;
        $this->_updateCollection = $updateCollection;
        $this->_themeConfig = $themeConfig;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Clean related contents to a theme (before save)
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function cleanThemeRelatedContent(\Magento\Event\Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\Core\Model\Theme) {
            return;
        }
        /** @var $theme \Magento\View\Design\ThemeInterface */
        if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
            throw new \Magento\Core\Exception(__('Theme isn\'t deletable.'));
        }
        $this->_themeImageFactory->create(array('theme' => $theme))->removePreviewImage();
        $this->_updateCollection->addThemeFilter($theme->getId())->delete();
    }

    /**
     * Check a theme, it's assigned to any of store
     *
     * @param \Magento\Event\Observer $observer
     */
    public function checkThemeIsAssigned(\Magento\Event\Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\Core\Model\Theme) {
            /** @var $theme \Magento\View\Design\ThemeInterface */
            if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
                $this->_eventDispatcher->dispatch('assigned_theme_changed', array('theme' => $this));
            }
        }
    }
}
