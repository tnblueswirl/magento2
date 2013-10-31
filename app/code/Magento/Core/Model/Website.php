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
 * Core Website model
 *
 * @method \Magento\Core\Model\Resource\Website _getResource()
 * @method \Magento\Core\Model\Resource\Website getResource()
 * @method \Magento\Core\Model\Website setCode(string $value)
 * @method string getName()
 * @method string getGroupTitle()
 * @method string getStoreTitle()
 * @method int getStoreId()
 * @method int getGroupId()
 * @method int getWebsiteId()
 * @method bool hasWebsiteId()
 * @method \Magento\Core\Model\Website setName(string $value)
 * @method int getSortOrder()
 * @method \Magento\Core\Model\Website setSortOrder(int $value)
 * @method \Magento\Core\Model\Website setDefaultGroupId(int $value)
 * @method int getIsDefault()
 * @method \Magento\Core\Model\Website setIsDefault(int $value)
 */
namespace Magento\Core\Model;

class Website extends \Magento\Core\Model\AbstractModel
{
    const ENTITY    = 'core_website';
    const CACHE_TAG = 'website';
    protected $_cacheTag = true;

    /**
     * @var string
     */
    protected $_eventPrefix = 'website';

    /**
     * @var string
     */
    protected $_eventObject = 'website';

    /**
     * Cache configuration array
     *
     * @var array
     */
    protected $_configCache = array();

    /**
     * Website Group Collection array
     *
     * @var array
     */
    protected $_groups;

    /**
     * Website group ids array
     *
     * @var array
     */
    protected $_groupIds = array();

    /**
     * The number of groups in a website
     *
     * @var int
     */
    protected $_groupsCount;

    /**
     * Website Store collection array
     *
     * @var array
     */
    protected $_stores;

    /**
     * Website store ids array
     *
     * @var array
     */
    protected $_storeIds = array();

    /**
     * Website store codes array
     *
     * @var array
     */
    protected $_storeCodes = array();

    /**
     * The number of stores in a website
     *
     * @var int
     */
    protected $_storesCount = 0;

    /**
     * Website default group
     *
     * @var \Magento\Core\Model\Store\Group
     */
    protected $_defaultGroup;

    /**
     * Website default store
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_defaultStore;

    /**
     * is can delete website
     *
     * @var bool
     */
    protected $_isCanDelete;

    /**
     * @var bool
     */
    private $_isReadOnly = false;

    /**
     * @var \Magento\Core\Model\Resource\Config\Data
     */
    protected $_configDataResource;

    /**
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Core\Model\Store\GroupFactory
     */
    protected $_storeGroupFactory;

    /**
     * @var \Magento\Core\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\Config\Data $configDataResource
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param \Magento\Core\Model\Store\GroupFactory $storeGroupFactory
     * @param \Magento\Core\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\Config\Data $configDataResource,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\StoreFactory $storeFactory,
        \Magento\Core\Model\Store\GroupFactory $storeGroupFactory,
        \Magento\Core\Model\WebsiteFactory $websiteFactory,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\App $app,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_configDataResource = $configDataResource;
        $this->_coreConfig = $coreConfig;
        $this->_storeFactory = $storeFactory;
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_storeManager = $storeManager;
        $this->_app = $app;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * init model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Website');
    }

    /**
     * Custom load
     *
     * @param int|string $id
     * @param string $field
     * @return \Magento\Core\Model\Website
     */
    public function load($id, $field = null)
    {
        if (!is_numeric($id) && is_null($field)) {
            $this->_getResource()->load($this, $id, 'code');
            return $this;
        }
        return parent::load($id, $field);
    }

    /**
     * Get website config data
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        if (!isset($this->_configCache[$path])) {

            $config = $this->_coreConfig->getValue($path, 'website', $this->getCode());
            if (!$config) {
                return false;
            }
            $this->_configCache[$path] = $config;
        }
        return $this->_configCache[$path];
    }

    /**
     * Load group collection and set internal data
     *
     */
    protected function _loadGroups()
    {
        $this->_groups = array();
        $this->_groupsCount = 0;
        foreach ($this->getGroupCollection() as $group) {
            $this->_groups[$group->getId()] = $group;
            $this->_groupIds[$group->getId()] = $group->getId();
            if ($this->getDefaultGroupId() == $group->getId()) {
                $this->_defaultGroup = $group;
            }
            $this->_groupsCount ++;
        }
    }

    /**
     * Set website groups
     *
     * @param array $groups
     * @return \Magento\Core\Model\Website
     */
    public function setGroups($groups)
    {
        $this->_groups = array();
        $this->_groupsCount = 0;
        foreach ($groups as $group) {
            $this->_groups[$group->getId()] = $group;
            $this->_groupIds[$group->getId()] = $group->getId();
            if ($this->getDefaultGroupId() == $group->getId()) {
                $this->_defaultGroup = $group;
            }
            $this->_groupsCount++;
        }
        return $this;
    }

    /**
     * Retrieve new (not loaded) Group collection object with website filter
     *
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function getGroupCollection()
    {
        return $this->_storeGroupFactory->create()
            ->getCollection()
            ->addWebsiteFilter($this->getId());
    }

    /**
     * Retrieve website groups
     *
     * @return \Magento\Core\Model\Store\Group[]
     */
    public function getGroups()
    {
        if (is_null($this->_groups)) {
            $this->_loadGroups();
        }
        return $this->_groups;
    }

    /**
     * Retrieve website group ids
     *
     * @return array
     */
    public function getGroupIds()
    {
        if (is_null($this->_groups)) {
            $this->_loadGroups();
        }
        return $this->_groupIds;
    }

    /**
     * Retrieve number groups in a website
     *
     * @return int
     */
    public function getGroupsCount()
    {
        if (is_null($this->_groups)) {
            $this->_loadGroups();
        }
        return $this->_groupsCount;
    }

    /**
     * Retrieve default group model
     *
     * @return \Magento\Core\Model\Store\Group
     */
    public function getDefaultGroup()
    {
        if (!$this->hasDefaultGroupId()) {
            return false;
        }
        if (is_null($this->_groups)) {
            $this->_loadGroups();
        }
        return $this->_defaultGroup;
    }

    /**
     * Load store collection and set internal data
     *
     */
    protected function _loadStores()
    {
        $this->_stores = array();
        $this->_storesCount = 0;
        foreach ($this->getStoreCollection() as $store) {
            $this->_stores[$store->getId()] = $store;
            $this->_storeIds[$store->getId()] = $store->getId();
            $this->_storeCodes[$store->getId()] = $store->getCode();
            if ($this->getDefaultGroup() && $this->getDefaultGroup()->getDefaultStoreId() == $store->getId()) {
                $this->_defaultStore = $store;
            }
            $this->_storesCount ++;
        }
    }

    /**
     * Set website stores
     *
     * @param array $stores
     */
    public function setStores($stores)
    {
        $this->_stores = array();
        $this->_storesCount = 0;
        foreach ($stores as $store) {
            $this->_stores[$store->getId()] = $store;
            $this->_storeIds[$store->getId()] = $store->getId();
            $this->_storeCodes[$store->getId()] = $store->getCode();
            if ($this->getDefaultGroup() && $this->getDefaultGroup()->getDefaultStoreId() == $store->getId()) {
                $this->_defaultStore = $store;
            }
            $this->_storesCount ++;
        }
    }

    /**
     * Retrieve new (not loaded) Store collection object with website filter
     *
     * @return \Magento\Core\Model\Resource\Store\Collection
     */
    public function getStoreCollection()
    {
        return $this->_storeFactory->create()
            ->getCollection()
            ->addWebsiteFilter($this->getId());
    }

    /**
     * Retrieve wersite store objects
     *
     * @return array
     */
    public function getStores()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_stores;
    }

    /**
     * Retrieve website store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storeIds;
    }

    /**
     * Retrieve website store codes
     *
     * @return array
     */
    public function getStoreCodes()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storeCodes;
    }

    /**
     * Retrieve number stores in a website
     *
     * @return int
     */
    public function getStoresCount()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storesCount;
    }

    /**
     * is can delete website
     *
     * @return bool
     */
    public function isCanDelete()
    {
        if ($this->_isReadOnly || !$this->getId()) {
            return false;
        }
        if (is_null($this->_isCanDelete)) {
            $this->_isCanDelete = ($this->_websiteFactory->create()->getCollection()->getSize() > 2)
                && !$this->getIsDefault();
        }
        return $this->_isCanDelete;
    }

    /**
     * Retrieve unique website-group-store key for collection with groups and stores
     *
     * @return string
     */
    public function getWebsiteGroupStore()
    {
        return join('-', array($this->getWebsiteId(), $this->getGroupId(), $this->getStoreId()));
    }

    public function getDefaultGroupId()
    {
        return $this->_getData('default_group_id');
    }

    public function getCode()
    {
        return $this->_getData('code');
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        $this->_configDataResource->clearWebsiteData($this);
        return parent::_beforeDelete();
    }

    /**
     * rewrite in order to clear configuration cache
     *
     * @return \Magento\Core\Model\Website
     */
    protected function _afterDelete()
    {
        $this->_storeManager->clearWebsiteCache($this->getId());
        parent::_afterDelete();
        $this->_coreConfig->removeCache();
        return $this;
    }

    /**
     * Retrieve website base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        if ($this->getConfig(\Magento\Core\Model\Store::XML_PATH_PRICE_SCOPE)
            == \Magento\Core\Model\Store::PRICE_SCOPE_GLOBAL
        ) {
            return $this->_app->getBaseCurrencyCode();
        } else {
            return $this->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE);
        }
    }

    /**
     * Retrieve website base currency
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        $currency = $this->getData('base_currency');
        if (is_null($currency)) {
            $currency = $this->_currencyFactory->create()->load($this->getBaseCurrencyCode());
            $this->setData('base_currency', $currency);
        }
        return $currency;
    }

    /**
     * Retrieve Default Website Store or null
     *
     * @return \Magento\Core\Model\Store
     */
    public function getDefaultStore()
    {
        // init stores if not loaded
        $this->getStores();
        return $this->_defaultStore;
    }

    /**
     * Retrieve default stores select object
     * Select fields website_id, store_id
     *
     * @param bool $withDefault include/exclude default admin website
     * @return \Magento\DB\Select
     */
    public function getDefaultStoresSelect($withDefault = false)
    {
        return $this->getResource()->getDefaultStoresSelect($withDefault);
    }

    /**
     * Get/Set isReadOnly flag
     *
     * @param bool $value
     * @return bool
     */
    public function isReadOnly($value = null)
    {
        if (null !== $value) {
            $this->_isReadOnly = (bool)$value;
        }
        return $this->_isReadOnly;
    }
}
