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

namespace Magento\Core\Model;

/**
 * Core Observer model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Observer
{
    /**
     * @var \Magento\Core\Model\Cache\Frontend\Pool
     */
    private $_cacheFrontendPool;

    /**
     * @var \Magento\Core\Model\Theme
     */
    private $_currentTheme;

    /**
     * @var \Magento\Core\Model\Page\Asset\Collection
     */
    private $_pageAssets;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\Page\Asset\PublicFileFactory
     */
    protected $_assetFileFactory;

    /**
     * @var \Magento\Core\Model\Theme\Registration
     */
    protected  $_registration;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Core\Model\Page $page
     * @param \Magento\Core\Model\ConfigInterface $config
     * @param \Magento\Core\Model\Page\Asset\PublicFileFactory $assetFileFactory
     * @param \Magento\Core\Model\Theme\Registration $registration
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Core\Model\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\View\DesignInterface $design,
        \Magento\Core\Model\Page $page,
        \Magento\Core\Model\ConfigInterface $config,
        \Magento\Core\Model\Page\Asset\PublicFileFactory $assetFileFactory,
        \Magento\Core\Model\Theme\Registration $registration,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_currentTheme = $design->getDesignTheme();
        $this->_pageAssets = $page->getAssets();
        $this->_config = $config;
        $this->_assetFileFactory = $assetFileFactory;
        $this->_registration = $registration;
        $this->_logger = $logger;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cleanCache(\Magento\Cron\Model\Schedule $schedule)
    {
        /** @var $cacheFrontend \Magento\Cache\FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            // Magento cache frontend does not support the 'old' cleaning mode, that's why backend is used directly
            $cacheFrontend->getBackend()->clean(\Zend_Cache::CLEANING_MODE_OLD);
        }
    }

    /**
     * Theme registration
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Core\Model\Observer
     */
    public function themeRegistration(\Magento\Event\Observer $observer)
    {
        $baseDir = $observer->getEvent()->getBaseDir();
        $pathPattern = $observer->getEvent()->getPathPattern();
        try {
            $this->_registration->register($baseDir, $pathPattern);
        } catch (\Magento\Core\Exception $e) {
            $this->_logger->logException($e);
        }
        return $this;
    }

    /**
     * Apply customized static files to frontend
     *
     * @param \Magento\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyThemeCustomization(\Magento\Event\Observer $observer)
    {
        /** @var $themeFile \Magento\Core\Model\Theme\File */
        foreach ($this->_currentTheme->getCustomization()->getFiles() as $themeFile) {
            try {
                $service = $themeFile->getCustomizationService();
                if ($service instanceof \Magento\Core\Model\Theme\Customization\FileAssetInterface) {
                    $asset = $this->_assetFileFactory->create(array(
                        'file'        => $themeFile->getFullPath(),
                        'contentType' => $service->getContentType()
                    ));
                    $this->_pageAssets->add($themeFile->getData('file_path'), $asset);
                }
            } catch (\InvalidArgumentException $e) {
                $this->_logger->logException($e);
            }
        }
    }

    /**
     * Rebuild whole config and save to fast storage
     *
     * @param  \Magento\Event\Observer $observer
     * @return \Magento\Core\Model\Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processReinitConfig(\Magento\Event\Observer $observer)
    {
        $this->_config->reinit();
        return $this;
    }
}
