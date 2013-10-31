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
 * Store model
 *
 * @method \Magento\Core\Model\Resource\Store _getResource()
 * @method \Magento\Core\Model\Resource\Store getResource()
 * @method \Magento\Core\Model\Store setId(string $value)
 * @method \Magento\Core\Model\Store setCode(string $value)
 * @method \Magento\Core\Model\Store setWebsiteId(int $value)
 * @method \Magento\Core\Model\Store setGroupId(int $value)
 * @method \Magento\Core\Model\Store setName(string $value)
 * @method int getSortOrder()
 * @method int getStoreId()
 * @method \Magento\Core\Model\Store setSortOrder(int $value)
 * @method \Magento\Core\Model\Store setIsActive(int $value)
 */
namespace Magento\Core\Model;

class Store extends \Magento\Core\Model\AbstractModel
{
    /**
     * Entity name
     */
    const ENTITY = 'core_store';

    /**
     * Custom entry point param
     */
    const CUSTOM_ENTRY_POINT_PARAM = 'custom_entry_point';

    /**#@+
     * Configuration paths
     */
    const XML_PATH_STORE_STORE_NAME         = 'general/store_information/name';
    const XML_PATH_STORE_STORE_PHONE        = 'general/store_information/phone';
    const XML_PATH_STORE_IN_URL             = 'web/url/use_store';
    const XML_PATH_USE_REWRITES             = 'web/seo/use_rewrites';
    const XML_PATH_UNSECURE_BASE_URL        = 'web/unsecure/base_url';
    const XML_PATH_SECURE_BASE_URL          = 'web/secure/base_url';
    const XML_PATH_SECURE_IN_FRONTEND       = 'web/secure/use_in_frontend';
    const XML_PATH_SECURE_IN_ADMINHTML      = 'web/secure/use_in_adminhtml';
    const XML_PATH_SECURE_BASE_LINK_URL     = 'web/secure/base_link_url';
    const XML_PATH_UNSECURE_BASE_LINK_URL   = 'web/unsecure/base_link_url';
    const XML_PATH_SECURE_BASE_LIB_URL      = 'web/secure/base_lib_url';
    const XML_PATH_UNSECURE_BASE_LIB_URL    = 'web/unsecure/base_lib_url';
    const XML_PATH_SECURE_BASE_STATIC_URL   = 'web/secure/base_static_url';
    const XML_PATH_UNSECURE_BASE_STATIC_URL = 'web/unsecure/base_static_url';
    const XML_PATH_SECURE_BASE_CACHE_URL    = 'web/secure/base_cache_url';
    const XML_PATH_UNSECURE_BASE_CACHE_URL  = 'web/unsecure/base_cache_url';
    const XML_PATH_SECURE_BASE_MEDIA_URL    = 'web/secure/base_media_url';
    const XML_PATH_UNSECURE_BASE_MEDIA_URL  = 'web/unsecure/base_media_url';
    const XML_PATH_OFFLOADER_HEADER         = 'web/secure/offloader_header';
    const XML_PATH_PRICE_SCOPE              = 'catalog/price/scope';
    /**#@- */

    /**
     * Price scope constants
     */
    const PRICE_SCOPE_GLOBAL              = 0;
    const PRICE_SCOPE_WEBSITE             = 1;

    /**#@+
     * Possible URL types
     */
    const URL_TYPE_LINK                   = 'link';
    const URL_TYPE_DIRECT_LINK            = 'direct_link';
    const URL_TYPE_WEB                    = 'web';
    const URL_TYPE_LIB                    = 'lib';
    const URL_TYPE_MEDIA                  = 'media';
    const URL_TYPE_STATIC                 = 'static';
    const URL_TYPE_CACHE                  = 'cache';
    const URL_TYPE_JS                     = 'js';
    /**#@-*/

    /**
     * Code constants
     */
    const DEFAULT_CODE                    = 'default';
    const ADMIN_CODE                      = 'admin';

    /**
     * Cache tag
     */
    const CACHE_TAG                       = 'store';

    /**
     * Cookie name
     */
    const COOKIE_NAME                     = 'store';

    /**
     * Cookie currency key
     */
    const COOKIE_CURRENCY                 = 'currency';

    /**
     * Script name, which returns all the images
     */
    const MEDIA_REWRITE_SCRIPT            = 'get.php/';

    /**
     * A placeholder for generating base URL
     */
    const BASE_URL_PLACEHOLDER            = '{{base_url}}';

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Cache flag
     *
     * @var boolean
     */
    protected $_cacheTag    = true;

    /**
     * Event prefix for model events
     *
     * @var string
     */
    protected $_eventPrefix = 'store';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'store';

    /**
     * Price filter
     *
     * @var \Magento\Directory\Model\Currency\Filter
     */
    protected $_priceFilter;

    /**
     * Group model
     *
     * @var \Magento\Core\Model\Store\Group
     */
    protected $_group;

    /**
     * Store configuration cache
     *
     * @var array|null
     */
    protected $_configCache = null;

    /**
     * Base nodes of store configuration cache
     *
     * @var array
     */
    protected $_configCacheBaseNodes = array();

    /**
     * Directory cache
     *
     * @var array
     */
    protected $_dirCache = array();

    /**
     * URL cache
     *
     * @var array
     */
    protected $_urlCache = array();

    /**
     * Base URL cache
     *
     * @var array
     */
    protected $_baseUrlCache = array();

    /**
     * Session entity
     *
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_session;

    /**
     * Flag that shows that backend URLs are secure
     *
     * @var boolean|null
     */
    protected $_isAdminSecure = null;

    /**
     * Flag that shows that frontend URLs are secure
     *
     * @var boolean|null
     */
    protected $_isFrontSecure = null;

    /**
     * Store frontend name
     *
     * @var string|null
     */
    protected $_frontendName = null;

    /**
     * Readonly flag
     *
     * @var bool
     */
    private $_isReadOnly = false;

    /**
     * Url model for current store
     *
     * @var \Magento\Core\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var bool
     */
    protected $_isCustomEntryPoint = false;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\Resource\Config\Data
     */
    protected $_configDataResource;

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDatabase = null;

    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\Url $url
     * @param \Magento\App\State $appState
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Model\Resource\Config\Data $configDataResource
     * @param \Magento\App\Dir $dir
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\Resource\Store $resource
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param bool $isCustomEntryPoint
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\Url $url,
        \Magento\App\State $appState,
        \Magento\App\RequestInterface $request,
        \Magento\Core\Model\Resource\Config\Data $configDataResource,
        \Magento\App\Dir $dir,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\Resource\Store $resource,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $isCustomEntryPoint = false,
        array $data = array()
    ) {
        $this->_coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_url = $url;
        $this->_configCacheType = $configCacheType;
        $this->_appState = $appState;
        $this->_request = $request;
        $this->_configDataResource = $configDataResource;
        $this->_isCustomEntryPoint = $isCustomEntryPoint;
        $this->_dir = $dir;
        $this->_coreConfig = $coreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
    }

    public function __sleep()
    {
        $properties = parent::__sleep();
        $properties = array_diff($properties, array(
            '_coreFileStorageDatabase',
            '_eventDispatcher',
            '_cacheManager',
            '_coreStoreConfig',
            '_coreConfig'
        ));
        return $properties;
    }

    /**
     * Init not serializable fields
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $this->_eventDispatcher = \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Event\ManagerInterface');
        $this->_cacheManager    = \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Model\CacheInterface');
        $this->_coreStoreConfig = \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Model\Store\Config');
        $this->_coreConfig = \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Model\Config');
        $this->_coreFileStorageDatabase = \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Helper\File\Storage\Database');
    }

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Store');
    }

    /**
     * Retrieve store session object
     *
     * @return \Magento\Core\Model\Session\AbstractSession
     */
    protected function _getSession()
    {
        if (!$this->_session) {
            $this->_session = \Magento\Core\Model\ObjectManager::getInstance()->create('Magento\Core\Model\Session')
                ->init('store_'.$this->getCode());
        }
        return $this->_session;
    }

    /**
     * Validation rules for store
     *
     * @return \Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $validator = new \Magento\Validator\Composite\VarienObject();

        $storeLabelRule = new \Zend_Validate_NotEmpty();
        $storeLabelRule->setMessage(
            __('Name is required'),
            \Zend_Validate_NotEmpty::IS_EMPTY
        );
        $validator->addRule($storeLabelRule, 'name');

        $storeCodeRule = new \Zend_Validate_Regex('/^[a-z]+[a-z0-9_]*$/');
        $storeCodeRule->setMessage(
            __('The store code may contain only letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter'),
            \Zend_Validate_Regex::NOT_MATCH
        );
        $validator->addRule($storeCodeRule, 'code');

        return $validator;
    }

    /**
     * Loading store data
     *
     * @param   mixed $key
     * @param   string $field
     * @return  \Magento\Core\Model\Store
     */
    public function load($key, $field = null)
    {
        if (!is_numeric($key) && is_null($field)) {
            $this->_getResource()->load($this, $key, 'code');
            return $this;
        }
        return parent::load($key, $field);
    }

    /**
     * Retrieve Store code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData('code');
    }

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function getConfig($path)
    {
        $config = $this->_coreConfig;
        $data = $config->getValue($path, 'store', $this->getCode());
        if (!$data && !$this->_appState->isInstalled()) {
            $data = $config->getValue($path, 'default');
        }
        if ($data === false) {
            return null;
        }
        return $data;
    }

    /**
     * Set config value for CURRENT model
     *
     * This value don't save in config
     *
     * @param string $path
     * @param mixed $value
     * @return \Magento\Core\Model\Store
     */
    public function setConfig($path, $value)
    {
        $this->_coreConfig->setValue($path, $value, 'store', $this->getCode());
        return $this;
    }

    /**
     * Set relation to the website
     *
     * @param \Magento\Core\Model\Website $website
     */
    public function setWebsite(\Magento\Core\Model\Website $website)
    {
        $this->setWebsiteId($website->getId());
    }

    /**
     * Retrieve store website
     *
     * @return \Magento\Core\Model\Website|bool
     */
    public function getWebsite()
    {
        if (is_null($this->getWebsiteId())) {
            return false;
        }
        return $this->_storeManager->getWebsite($this->getWebsiteId());
    }

    /**
     * Retrieve url using store configuration specific
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        /** @var $url \Magento\Core\Model\Url */
        $url = $this->getUrlModel()
            ->setStore($this);
        if ($this->_storeManager->getStore()->getId() != $this->getId()) {
            $params['_store_to_url'] = true;
        }

        return $url->getUrl($route, $params);
    }

    /**
     * Retrieve base URL
     *
     * @param string $type
     * @param boolean|null $secure
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getBaseUrl($type = self::URL_TYPE_LINK, $secure = null)
    {
        $cacheKey = $type . '/' . (is_null($secure) ? 'null' : ($secure ? 'true' : 'false'));
        if (!isset($this->_baseUrlCache[$cacheKey])) {
            $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool)$secure;
            switch ($type) {
                case self::URL_TYPE_WEB:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_URL : self::XML_PATH_UNSECURE_BASE_URL;
                    $url = $this->getConfig($path);
                    break;

                case self::URL_TYPE_LINK:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LINK_URL : self::XML_PATH_UNSECURE_BASE_LINK_URL;
                    $url = $this->getConfig($path);
                    $url = $this->_updatePathUseRewrites($url);
                    $url = $this->_updatePathUseStoreView($url);
                    break;

                case self::URL_TYPE_DIRECT_LINK:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LINK_URL : self::XML_PATH_UNSECURE_BASE_LINK_URL;
                    $url = $this->getConfig($path);
                    $url = $this->_updatePathUseRewrites($url);
                    break;

                case self::URL_TYPE_LIB:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_LIB_URL : self::XML_PATH_UNSECURE_BASE_LIB_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(\Magento\App\Dir::PUB_LIB);
                    }
                    break;

                case self::URL_TYPE_STATIC:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_STATIC_URL : self::XML_PATH_UNSECURE_BASE_STATIC_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(\Magento\App\Dir::STATIC_VIEW);
                    }
                    break;

                case self::URL_TYPE_CACHE:
                    $path = $secure ? self::XML_PATH_SECURE_BASE_CACHE_URL : self::XML_PATH_UNSECURE_BASE_CACHE_URL;
                    $url = $this->getConfig($path);
                    if (!$url) {
                        $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                            . $this->_dir->getUri(\Magento\App\Dir::PUB_VIEW_CACHE);
                    }
                    break;

                case self::URL_TYPE_MEDIA:
                    $url = $this->_getMediaScriptUrl($this->_dir, $secure);
                    if (!$url) {
                        $path = $secure ? self::XML_PATH_SECURE_BASE_MEDIA_URL : self::XML_PATH_UNSECURE_BASE_MEDIA_URL;
                        $url = $this->getConfig($path);
                        if (!$url) {
                            $url = $this->getBaseUrl(self::URL_TYPE_WEB, $secure)
                                . $this->_dir->getUri(\Magento\App\Dir::MEDIA);
                        }
                    }
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid base url type');
            }

            if (false !== strpos($url, \Magento\Core\Model\Store::BASE_URL_PLACEHOLDER)) {
                $distroBaseUrl = $this->_request->getDistroBaseUrl();
                $url = str_replace(\Magento\Core\Model\Store::BASE_URL_PLACEHOLDER, $distroBaseUrl, $url);
            }

            $this->_baseUrlCache[$cacheKey] = rtrim($url, '/') . '/';
        }

        return $this->_baseUrlCache[$cacheKey];
    }

    /**
     * Remove script file name from url in case when server rewrites are enabled
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseRewrites($url)
    {
        if ($this->isAdmin()
            || !$this->getConfig(self::XML_PATH_USE_REWRITES)
            || !$this->_appState->isInstalled()
        ) {
            if ($this->_isCustomEntryPoint()) {
                $indexFileName = 'index.php';
            } else {
                $indexFileName = basename($_SERVER['SCRIPT_FILENAME']);
            }
            $url .= $indexFileName . '/';
        }
        return $url;
    }

    /**
     * Check if used entry point is custom
     *
     * @return bool
     */
    protected function _isCustomEntryPoint()
    {
        return $this->_isCustomEntryPoint;
    }

    /**
     * Retrieve URL for media catalog
     *
     * If we use Database file storage and server doesn't support rewrites (.htaccess in media folder)
     * we have to put name of fetching media script exactly into URL
     *
     * @param \Magento\App\Dir $dirs
     * @param bool $secure
     * @return string|bool
     */
    protected function _getMediaScriptUrl(\Magento\App\Dir $dirs, $secure)
    {
        if (!$this->getConfig(self::XML_PATH_USE_REWRITES)
            && $this->_coreFileStorageDatabase->checkDbUsage()
        ) {
            return $this->getBaseUrl(self::URL_TYPE_WEB, $secure) . $dirs->getUri(\Magento\App\Dir::PUB)
            . '/' . self::MEDIA_REWRITE_SCRIPT;
        }
        return false;
    }

    /**
     * Add store code to url in case if it is enabled in configuration
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseStoreView($url)
    {
        if ($this->isUseStoreInUrl()) {
            $url .= $this->getCode() . '/';
        }
        return $url;
    }

    /**
     * Returns whether url forming scheme prepends url path with store view code
     *
     * @return boolean
     */
    public function isUseStoreInUrl()
    {
        return $this->_appState->isInstalled()
        && $this->getConfig(self::XML_PATH_STORE_IN_URL)
        && !$this->isAdmin();
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Check if store is admin store
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getId() == \Magento\Core\Model\AppInterface::ADMIN_STORE_ID;
    }


    /**
     * Check if backend URLs should be secure
     *
     * @return boolean
     */
    public function isAdminUrlSecure()
    {
        if ($this->_isAdminSecure === null) {
            $this->_isAdminSecure = (boolean) (int) (string) $this->_coreConfig
                ->getValue(\Magento\Core\Model\Url::XML_PATH_SECURE_IN_ADMIN, 'default');
        }
        return $this->_isAdminSecure;
    }

    /**
     * Check if frontend URLs should be secure
     *
     * @return boolean
     */
    public function isFrontUrlSecure()
    {
        if ($this->_isFrontSecure === null) {
            $this->_isFrontSecure = $this->_coreStoreConfig->getConfigFlag(\Magento\Core\Model\Url::XML_PATH_SECURE_IN_FRONT,
                $this->getId());
        }
        return $this->_isFrontSecure;
    }

    /**
     * Check if request was secure
     *
     * @return boolean
     */
    public function isCurrentlySecure()
    {
        $standardRule = !empty($_SERVER['HTTPS']) && ('off' != $_SERVER['HTTPS']);
        $offloaderHeader = trim((string) $this->_coreConfig->getValue(self::XML_PATH_OFFLOADER_HEADER, 'default'));

        if ((!empty($offloaderHeader) && !empty($_SERVER[$offloaderHeader])) || $standardRule) {
            return true;
        }

        if ($this->_appState->isInstalled()) {
            $secureBaseUrl = $this->_coreStoreConfig->getConfig(\Magento\Core\Model\Url::XML_PATH_SECURE_URL);

            if (!$secureBaseUrl) {
                return false;
            }

            $uri = \Zend_Uri::factory($secureBaseUrl);
            $port = $uri->getPort();
            $isSecure = ($uri->getScheme() == 'https')
                && isset($_SERVER['SERVER_PORT'])
                && ($port == $_SERVER['SERVER_PORT']);
            return $isSecure;
        } else {
            $isSecure = isset($_SERVER['SERVER_PORT']) && (443 == $_SERVER['SERVER_PORT']);
            return $isSecure;
        }
    }

    /*************************************************************************************
     * Store currency interface
     */

    /**
     * Retrieve store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        $configValue = $this->getConfig(\Magento\Core\Model\Store::XML_PATH_PRICE_SCOPE);
        if ($configValue == \Magento\Core\Model\Store::PRICE_SCOPE_GLOBAL) {
            return \Magento\Core\Model\ObjectManager::getInstance()
                ->get('Magento\Core\Model\App')->getBaseCurrencyCode();
        } else {
            return $this->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE);
        }
    }

    /**
     * Retrieve store base currency
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        $currency = $this->getData('base_currency');
        if (is_null($currency)) {
            $currency = \Magento\Core\Model\ObjectManager::getInstance()->create('Magento\Directory\Model\Currency')
                ->load($this->getBaseCurrencyCode());
            $this->setData('base_currency', $currency);
        }
        return $currency;
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        $result = $this->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_DEFAULT);
        return $result;
    }

    /**
     * Retrieve store default currency
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getDefaultCurrency()
    {
        $currency = $this->getData('default_currency');
        if (is_null($currency)) {
            $currency = \Magento\Core\Model\ObjectManager::getInstance()->create('Magento\Directory\Model\Currency')
                ->load($this->getDefaultCurrencyCode());
            $this->setData('default_currency', $currency);
        }
        return $currency;
    }

    /**
     * Set current store currency code
     *
     * @param   string $code
     * @return  string
     */
    public function setCurrentCurrencyCode($code)
    {
        $code = strtoupper($code);
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            $this->_getSession()->setCurrencyCode($code);
            if ($code == $this->getDefaultCurrency()) {
                \Magento\Core\Model\ObjectManager::getInstance()
                    ->get('Magento\Core\Model\App')->getCookie()->delete(self::COOKIE_CURRENCY, $code);
            } else {
                \Magento\Core\Model\ObjectManager::getInstance()
                    ->get('Magento\Core\Model\App')->getCookie()->set(self::COOKIE_CURRENCY, $code);
            }
        }
        return $this;
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        // try to get currently set code among allowed
        $code = $this->_getSession()->getCurrencyCode();
        if (empty($code)) {
            $code = $this->getDefaultCurrencyCode();
        }
        if (in_array($code, $this->getAvailableCurrencyCodes(true))) {
            return $code;
        }

        // take first one of allowed codes
        $codes = array_values($this->getAvailableCurrencyCodes(true));
        if (empty($codes)) {
            // return default code, if no codes specified at all
            return $this->getDefaultCurrencyCode();
        }
        return array_shift($codes);
    }

    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        $codes = $this->getData('available_currency_codes');
        if (is_null($codes)) {
            $codes = explode(',', $this->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_ALLOW));
            // add base currency, if it is not in allowed currencies
            $baseCurrencyCode = $this->getBaseCurrencyCode();
            if (!in_array($baseCurrencyCode, $codes)) {
                $codes[] = $baseCurrencyCode;

                // save base currency code index for further usage
                $disallowedBaseCodeIndex = array_keys($codes);
                $disallowedBaseCodeIndex = array_pop($disallowedBaseCodeIndex);
                $this->setData('disallowed_base_currency_code_index', $disallowedBaseCodeIndex);
            }
            $this->setData('available_currency_codes', $codes);
        }

        // remove base currency code, if it is not allowed by config (optional)
        if ($skipBaseNotAllowed) {
            $disallowedBaseCodeIndex = $this->getData('disallowed_base_currency_code_index');
            if (null !== $disallowedBaseCodeIndex) {
                unset($codes[$disallowedBaseCodeIndex]);
            }
        }
        return $codes;
    }

    /**
     * Retrieve store current currency
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getCurrentCurrency()
    {
        $currency = $this->getData('current_currency');

        if (is_null($currency)) {
            $currency = \Magento\Core\Model\ObjectManager::getInstance()->create('Magento\Directory\Model\Currency')
                ->load($this->getCurrentCurrencyCode());
            $baseCurrency = $this->getBaseCurrency();

            if (! $baseCurrency->getRate($currency)) {
                $currency = $baseCurrency;
                $this->setCurrentCurrencyCode($baseCurrency->getCode());
            }

            $this->setData('current_currency', $currency);
        }

        return $currency;
    }

    /**
     * Retrieve current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->getBaseCurrency()->getRate($this->getCurrentCurrency());
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param   double $price
     * @param   boolean $format             Format price to currency format
     * @param   boolean $includeContainer   Enclose into <span class="price"><span>
     * @return  double
     */
    public function convertPrice($price, $format = false, $includeContainer = true)
    {
        if ($this->getCurrentCurrency() && $this->getBaseCurrency()) {
            $value = $this->getBaseCurrency()->convert($price, $this->getCurrentCurrency());
        } else {
            $value = $price;
        }

        if ($this->getCurrentCurrency() && $format) {
            $value = $this->formatPrice($value, $includeContainer);
        }
        return $value;
    }

    /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        return round($price, 2);
    }

    /**
     * Format price with currency filter (taking rate into consideration)
     *
     * @param   double $price
     * @param   bool $includeContainer
     * @return  string
     */
    public function formatPrice($price, $includeContainer = true)
    {
        if ($this->getCurrentCurrency()) {
            return $this->getCurrentCurrency()->format($price, array(), $includeContainer);
        }
        return $price;
    }

    /**
     * Get store price filter
     *
     * @return \Magento\Filter\Sprintf
     */
    public function getPriceFilter()
    {
        if (!$this->_priceFilter) {
            if ($this->getBaseCurrency() && $this->getCurrentCurrency()) {
                $this->_priceFilter = $this->getCurrentCurrency()->getFilter();
                $this->_priceFilter->setRate($this->getBaseCurrency()->getRate($this->getCurrentCurrency()));
            } elseif ($this->getDefaultCurrency()) {
                $this->_priceFilter = $this->getDefaultCurrency()->getFilter();
            } else {
                $this->_priceFilter = new \Magento\Filter\Sprintf('%s', 2);
            }
        }
        return $this->_priceFilter;
    }

    /**
     * Retrieve root category identifier
     *
     * @return int
     */
    public function getRootCategoryId()
    {
        if (!$this->getGroup()) {
            return 0;
        }
        return $this->getGroup()->getRootCategoryId();
    }

    /**
     * Set group model for store
     *
     * @param \Magento\Core\Model\Store\Group $group
     */
    public function setGroup($group)
    {
        $this->setGroupId($group->getId());
    }

    /**
     * Retrieve group model
     *
     * @return \Magento\Core\Model\Store\Group|bool
     */
    public function getGroup()
    {
        if (is_null($this->getGroupId())) {
            return false;
        }
        return $this->_storeManager->getGroup($this->getGroupId());
    }

    /**
     * Retrieve website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_getData('website_id');
    }

    /**
     * Retrieve group identifier
     *
     * @return string|int|null
     */
    public function getGroupId()
    {
        return $this->_getData('group_id');
    }

    /**
     * Retrieve default group identifier
     *
     * @return string|int|null
     */
    public function getDefaultGroupId()
    {
        return $this->_getData('default_group_id');
    }

    /**
     * Check if store can be deleted
     *
     * @return boolean
     */
    public function isCanDelete()
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getGroup()->getDefaultStoreId() != $this->getId();
    }

    /**
     * Retrieve current url for store
     *
     * @param bool|string $fromStore
     * @return string
     */
    public function getCurrentUrl($fromStore = true)
    {
        $sidQueryParam = $this->_getSession()->getSessionIdQueryParam();
        $requestString = $this->getUrlModel()->escape(ltrim(
            \Magento\Core\Model\ObjectManager::getInstance()
                ->get('Magento\Core\Model\App')->getRequest()->getRequestString(),
            '/'
        ));

        $storeUrl = $this->_storeManager->getStore()->isCurrentlySecure()
            ? $this->getUrl('', array('_secure' => true))
            : $this->getUrl('');

        if (!filter_var($storeUrl, FILTER_VALIDATE_URL)) {
            return $storeUrl;
        }

        $storeParsedUrl = parse_url($storeUrl);

        $storeParsedQuery = array();
        if (isset($storeParsedUrl['query'])) {
            parse_str($storeParsedUrl['query'], $storeParsedQuery);
        }

        $currQuery = $this->_request->getQuery();
        if (isset($currQuery[$sidQueryParam]) && !empty($currQuery[$sidQueryParam])
            && $this->_getSession()->getSessionIdForHost($storeUrl) != $currQuery[$sidQueryParam]
        ) {
            unset($currQuery[$sidQueryParam]);
        }

        foreach ($currQuery as $k => $v) {
            $storeParsedQuery[$k] = $v;
        }

        if (!$this->isUseStoreInUrl()) {
            $storeParsedQuery['___store'] = $this->getCode();
        }
        if ($fromStore !== false) {
            $storeParsedQuery['___from_store'] = $fromStore === true
                ? $this->_storeManager->getStore()->getCode()
                : $fromStore;
        }

        return $storeParsedUrl['scheme'] . '://' . $storeParsedUrl['host']
        . (isset($storeParsedUrl['port']) ? ':' . $storeParsedUrl['port'] : '')
        . $storeParsedUrl['path'] . $requestString
        . ($storeParsedQuery ? '?'.http_build_query($storeParsedQuery, '', '&amp;') : '');
    }

    /**
     * Check if store is active
     *
     * @return boolean|null
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * Retrieve store name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Protect delete from non admin area
     *
     * Register indexing event before delete store
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        \Magento\Core\Model\ObjectManager::getInstance()->get('Magento\Index\Model\Indexer')
            ->logEvent($this, self::ENTITY, \Magento\Index\Model\Event::TYPE_DELETE);
        $this->_configDataResource->clearStoreData(array($this->getId()));
        return parent::_beforeDelete();
    }

    /**
     * rewrite in order to clear configuration cache
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->_configCacheType->clean();
        return $this;
    }

    /**
     * Init indexing process after store delete commit
     *
     * @return \Magento\Core\Model\Store
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        \Magento\Core\Model\ObjectManager::getInstance()->get('Magento\Index\Model\Indexer')
            ->indexEvents(self::ENTITY, \Magento\Index\Model\Event::TYPE_DELETE);
        return $this;
    }

    /**
     * Reinit and reset Config Data
     *
     * @return \Magento\Core\Model\Store
     */
    public function resetConfig()
    {
        $this->_coreConfig->reinit();
        $this->_dirCache        = array();
        $this->_baseUrlCache    = array();
        $this->_urlCache        = array();

        return $this;
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
            $this->_isReadOnly = (bool) $value;
        }
        return $this->_isReadOnly;
    }

    /**
     * Retrieve storegroup name
     *
     * @return string
     */
    public function getFrontendName()
    {
        if (is_null($this->_frontendName)) {
            $storeGroupName = (string) $this->_coreStoreConfig->getConfig('general/store_information/name', $this);
            $this->_frontendName = (!empty($storeGroupName)) ? $storeGroupName : $this->getGroup()->getName();
        }
        return $this->_frontendName;
    }

    /**
     * Set url model for current store
     *
     * @param \Magento\Core\Model\Url $urlModel
     * @return \Magento\Core\Model\Store
     */
    public function setUrlModel($urlModel)
    {
        $this->_url = $urlModel;
        return $this;
    }

    /**
     * Get url model by class name for current store
     *
     * @return \Magento\Core\Model\Url
     */
    public function getUrlModel()
    {
        return $this->_url;
    }
}
