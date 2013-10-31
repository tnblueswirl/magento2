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
 * @package     Magento_Persistent
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Persistent Session Observer
 */
namespace Magento\Persistent\Model\Observer;

class Session
{
    /**
     * Create/Update and Load session when customer log in
     *
     * @param \Magento\Event\Observer $observer
     */
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * Session factory
     *
     * @var \Magento\Persistent\Model\SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Cookie model
     *
     * @var \Magento\Core\Model\Cookie
     */
    protected $_cookie;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\Cookie $cookie
     * @param \Magento\Persistent\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\Cookie $cookie,
        \Magento\Persistent\Model\SessionFactory $sessionFactory
    ) {
        $this->_persistentData = $persistentData;
        $this->_persistentSession = $persistentSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_cookie = $cookie;
        $this->_sessionFactory = $sessionFactory;
    }

    public function synchronizePersistentOnLogin(\Magento\Event\Observer $observer)
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid (remove persistent cookie for invalid customer)
        if (!$customer
            || !$customer->getId()
            || !$this->_persistentSession->isRememberMeChecked()
        ) {
            $this->_sessionFactory->create()->removePersistentCookie();
            return;
        }

        $persistentLifeTime = $this->_persistentData->getLifeTime();
        // Delete persistent session, if persistent could not be applied
        if ($this->_persistentData->isEnabled() && ($persistentLifeTime <= 0)) {
            // Remove current customer persistent session
            $this->_sessionFactory->create()->deleteByCustomerId($customer->getId());
            return;
        }

        /** @var $sessionModel \Magento\Persistent\Model\Session */
        $sessionModel = $this->_persistentSession->getSession();

        // Check if session is wrong or not exists, so create new session
        if (!$sessionModel->getId() || ($sessionModel->getCustomerId() != $customer->getId())) {
            /** @var \Magento\Persistent\Model\Session $sessionModel */
            $sessionModel = $this->_sessionFactory->create();
            $sessionModel->setLoadExpired()
                ->loadByCustomerId($customer->getId());
            if (!$sessionModel->getId()) {
                /** @var \Magento\Persistent\Model\Session $sessionModel */
                $sessionModel = $this->_sessionFactory->create();
                $sessionModel->setCustomerId($customer->getId())
                    ->save();
            }

            $this->_persistentSession->setSession($sessionModel);
        }

        // Set new cookie
        if ($sessionModel->getId()) {
            $this->_cookie->set(
                \Magento\Persistent\Model\Session::COOKIE_NAME,
                $sessionModel->getKey(),
                $persistentLifeTime
            );
        }
    }

    /**
     * Unload persistent session (if set in config)
     *
     * @param \Magento\Event\Observer $observer
     */
    public function synchronizePersistentOnLogout(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->isEnabled() || !$this->_persistentData->getClearOnLogout()) {
            return;
        }

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();
        // Check if customer is valid
        if (!$customer || !$customer->getId()) {
            return;
        }

        $this->_sessionFactory->create()->removePersistentCookie();

        // Unset persistent session
        $this->_persistentSession->setSession(null);
    }

    /**
     * Synchronize persistent session info
     *
     * @param \Magento\Event\Observer $observer
     */
    public function synchronizePersistentInfo(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $sessionModel \Magento\Persistent\Model\Session */
        $sessionModel = $this->_persistentSession->getSession();

        /** @var $request \Magento\App\RequestInterface */
        $request = $observer->getEvent()->getFront()->getRequest();

        // Quote Id could be changed only by logged in customer
        if ($this->_customerSession->isLoggedIn()
            || ($request && $request->getActionName() == 'logout' && $request->getControllerName() == 'account')
        ) {
            $sessionModel->save();
        }
    }

    /**
     * Set Checked status of "Remember Me"
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setRememberMeCheckedStatus(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentData->isRememberMeEnabled()
        ) {
            return;
        }

        /** @var $controllerAction \Magento\Core\Controller\Varien\Action */
        $controllerAction = $observer->getEvent()->getControllerAction();
        if ($controllerAction) {
            $rememberMeCheckbox = $controllerAction->getRequest()->getPost('persistent_remember_me');
            $this->_persistentSession->setRememberMeChecked((bool)$rememberMeCheckbox);
            if (
                $controllerAction->getFullActionName() == 'checkout_onepage_saveBilling'
                    || $controllerAction->getFullActionName() == 'customer_account_createpost'
            ) {
                $this->_checkoutSession->setRememberMeChecked((bool)$rememberMeCheckbox);
            }
        }
    }

    /**
     * Renew persistent cookie
     *
     * @param \Magento\Event\Observer $observer
     */
    public function renewCookie(\Magento\Event\Observer $observer)
    {
        if (!$this->_persistentData->canProcess($observer)
            || !$this->_persistentData->isEnabled()
            || !$this->_persistentSession->isPersistent()
        ) {
            return;
        }

        /** @var $controllerAction \Magento\Core\Controller\Front\Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($this->_customerSession->isLoggedIn()
            || $controllerAction->getFullActionName() == 'customer_account_logout'
        ) {
            $this->_cookie->renew(
                \Magento\Persistent\Model\Session::COOKIE_NAME,
                $this->_persistentData->getLifeTime()
            );
        }
    }
}
