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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\AdminNotification\Model\System\Message;

class Security
    implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * Cache kay for saving verification result
     */
    const VERIFICATION_RESULT_CACHE_KEY = 'configuration_files_access_level_verification';

    /**
     * File path for verification
     *
     * @var string
     */
    private $_filePath = 'app/etc/local.xml';

    /**
     * Time out for HTTP verification request
     * @var int
     */
    private $_verificationTimeOut  = 2;

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\HTTP\Adapter\CurlFactory
     */
    protected $_curlFactory;

    /**
     * @param \Magento\Core\Model\CacheInterface $cache
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\HTTP\Adapter\CurlFactory $curlFactory
     */
    public function __construct(
        \Magento\Core\Model\CacheInterface $cache,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\Config $config,
        \Magento\HTTP\Adapter\CurlFactory $curlFactory
    ) {
        $this->_cache = $cache;
        $this->_storeConfig = $storeConfig;
        $this->_config = $config;
        $this->_curlFactory = $curlFactory;
    }

    /**
     * Check verification result and return true if system must to show notification message
     *
     * @return bool
     */
    private function _canShowNotification()
    {
        if ($this->_cache->load(self::VERIFICATION_RESULT_CACHE_KEY)) {
            return false;
        }

        if ($this->_isFileAccessible()) {
            return true;
        }

        $adminSessionLifetime = (int) $this->_storeConfig->getConfig('admin/security/session_lifetime');
        $this->_cache->save(true, self::VERIFICATION_RESULT_CACHE_KEY, array(), $adminSessionLifetime);
        return false;
    }

    /**
     * If file is accessible return true or false
     *
     * @return bool
     */
    private function _isFileAccessible()
    {
        $unsecureBaseURL = $this->_config->getValue(
            \Magento\Core\Model\Store::XML_PATH_UNSECURE_BASE_URL,
            'default'
        );

        /** @var $http \Magento\HTTP\Adapter\Curl */
        $http = $this->_curlFactory->create();
        $http->setConfig(array('timeout' => $this->_verificationTimeOut));
        $http->write(\Zend_Http_Client::POST, $unsecureBaseURL . $this->_filePath);
        $responseBody = $http->read();
        $responseCode = \Zend_Http_Response::extractCode($responseBody);
        $http->close();

        return $responseCode == 200;
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return 'security';
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_canShowNotification();
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return __('Your web server is configured incorrectly. As a result, configuration files with sensitive information are accessible from the outside. Please contact your hosting provider.');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_CRITICAL;
    }
}
