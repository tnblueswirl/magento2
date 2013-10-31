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
namespace Magento\Backend\Model\Menu;

class Config
{
    const CACHE_ID = 'backend_menu_config';
    const CACHE_MENU_OBJECT = 'backend_menu_object';

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Backend\Model\MenuFactory
     */
    protected $_menuFactory;
    /**
     * Menu model
     *
     * @var \Magento\Backend\Model\Menu
     */
    protected $_menu;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Backend\Model\Menu\Config\Reader
     */
    protected $_configReader;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Menu\AbstractDirector
     */
    protected $_director;

    /**
     * @param \Magento\Backend\Model\Menu\Builder $menuBuilder
     * @param \Magento\Backend\Model\Menu\AbstractDirector $menuDirector
     * @param \Magento\Backend\Model\MenuFactory $menuFactory
     * @param \Magento\Backend\Model\Menu\Config\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Model\Menu\Builder $menuBuilder,
        \Magento\Backend\Model\Menu\AbstractDirector $menuDirector,
        \Magento\Backend\Model\MenuFactory $menuFactory,
        \Magento\Backend\Model\Menu\Config\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_menuBuilder = $menuBuilder;
        $this->_director = $menuDirector;
        $this->_configCacheType = $configCacheType;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;
        $this->_menuFactory = $menuFactory;
        $this->_configReader = $configReader;
        $this->_storeManager = $storeManager;
    }

    /**
     * Build menu model from config
     *
     * @return \Magento\Backend\Model\Menu
     * @throws \InvalidArgumentException|BadMethodCallException|OutOfRangeException|Exception
     */
    public function getMenu()
    {
        $store = $this->_storeManager->getStore();
        $this->_logger->addStoreLog(\Magento\Backend\Model\Menu::LOGGER_KEY, $store);
        try {
            $this->_initMenu();
            return $this->_menu;
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\BadMethodCallException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\OutOfRangeException $e) {
            $this->_logger->logException($e);
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Initialize menu object
     *
     * @return void
     */
    protected function _initMenu()
    {
        if (!$this->_menu) {
            $this->_menu = $this->_menuFactory->create();

            $cache = $this->_configCacheType->load(self::CACHE_MENU_OBJECT);
            if ($cache) {
                $this->_menu->unserialize($cache);
                return;
            }

            $this->_director->direct(
                $this->_configReader->read(\Magento\Core\Model\App\Area::AREA_ADMINHTML),
                $this->_menuBuilder,
                $this->_logger
            );
            $this->_menu = $this->_menuBuilder->getResult($this->_menu);

            $this->_configCacheType->save($this->_menu->serialize(), self::CACHE_MENU_OBJECT);
        }
    }
}
