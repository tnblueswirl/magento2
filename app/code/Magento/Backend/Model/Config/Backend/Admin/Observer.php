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

namespace Magento\Backend\Model\Config\Backend\Admin;

class Observer
{
    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_backendData = $backendData;
        $this->_coreRegistry = $coreRegistry;
        $this->_authSession = $authSession;
        $this->_app = $app;
        $this->_storeManager = $storeManager;
    }

    /**
     * Log out user and redirect him to new admin custom url
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function afterCustomUrlChanged()
    {
        if (is_null($this->_coreRegistry->registry('custom_admin_path_redirect'))) {
            return;
        }

        $this->_authSession->unsetAll();
        $this->_authSession->getCookie()->delete($this->_authSession->getSessionName());

        $route = $this->_backendData->getAreaFrontName();

        $this->_app->getResponse()
            ->setRedirect($this->_storeManager->getStore()->getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
