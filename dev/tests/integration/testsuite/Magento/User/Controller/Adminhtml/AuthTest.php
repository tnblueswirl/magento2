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
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\User\Controller\Adminhtml;

/**
 * Test class for \Magento\User\Controller\Adminhtml\Auth
 *
 * @magentoAppArea adminhtml
 */
class AuthTest extends \Magento\Backend\Utility\Controller
{
    /**
     * Test form existence
     * @covers \Magento\User\Controller\Adminhtml\Auth::forgotpasswordAction
     */
    public function testFormForgotpasswordAction()
    {
        $this->dispatch('backend/admin/auth/forgotpassword');
        $expected = 'Forgot your user name or password?';
        $this->assertContains($expected, $this->getResponse()->getBody());
    }

    /**
     * Test redirection to startup page after success password recovering posting
     *
     * @covers \Magento\User\Controller\Adminhtml\Auth::forgotpasswordAction
     */
    public function testForgotpasswordAction()
    {
        $this->getRequest()->setPost('email', 'test@test.com');
        $this->dispatch('backend/admin/auth/forgotpassword');
        $this->assertRedirect($this->equalTo(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\Backend\Helper\Data')->getHomePageUrl()
        ));
    }

    /**
     * Test reset password action
     *
     * @covers \Magento\User\Controller\Adminhtml\Auth::resetPasswordAction
     * @covers \Magento\User\Controller\Adminhtml\Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testResetPasswordAction()
    {
        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->loadByUsername('dummy_username');
        $this->assertNotEmpty($user->getId(), 'Broken fixture');
        $resetPasswordToken = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\User\Helper\Data')
            ->generateResetPasswordLinkToken();
        $user->changeResetPasswordLinkToken($resetPasswordToken);
        $user->save();

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId());
        $this->dispatch('backend/admin/auth/resetpassword');

        $this->assertEquals('adminhtml', $this->getRequest()->getRouteName());
        $this->assertEquals('auth', $this->getRequest()->getControllerName());
        $this->assertEquals('resetpassword', $this->getRequest()->getActionName());

        $this->assertContains($resetPasswordToken, $this->getResponse()->getBody());
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\Auth::resetPasswordAction
     * @covers \Magento\User\Controller\Adminhtml\Auth::_validateResetPasswordLinkToken
     */
    public function testResetPasswordActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpassword');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), \Magento\Core\Model\Message::ERROR
        );
        $this->assertRedirect();
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\Auth::resetPasswordPostAction
     * @covers \Magento\User\Controller\Adminhtml\Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testResetPasswordPostAction()
    {
        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->loadByUsername('dummy_username');
        $this->assertNotEmpty($user->getId(), 'Broken fixture');
        $resetPasswordToken = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\User\Helper\Data')
            ->generateResetPasswordLinkToken();
        $user->changeResetPasswordLinkToken($resetPasswordToken);
        $user->save();

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', $newDummyPassword);

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertRedirect($this->equalTo(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Helper\Data')
                ->getHomePageUrl()
        ));

        /** @var $user \Magento\User\Model\User */
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->loadByUsername('dummy_username');
        $this->assertTrue(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\Data')
                ->validateHash($newDummyPassword, $user->getPassword())
        );
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\Auth::resetPasswordPostAction
     * @covers \Magento\User\Controller\Adminhtml\Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithDummyToken()
    {
        $this->getRequest()->setQuery('token', 'dummy')->setQuery('id', 1);
        $this->dispatch('backend/admin/auth/resetpasswordpost');
        $this->assertSessionMessages(
            $this->equalTo(array('Your password reset link has expired.')), \Magento\Core\Model\Message::ERROR
        );
        $this->assertRedirect($this->equalTo(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Helper\Data')
                ->getHomePageUrl()
        ));
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\Auth::resetPasswordPostAction
     * @covers \Magento\User\Controller\Adminhtml\Auth::_validateResetPasswordLinkToken
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testResetPasswordPostActionWithInvalidPassword()
    {
        $user = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\User')->loadByUsername('dummy_username');
        $resetPasswordToken = null;
        if ($user->getId()) {
            $resetPasswordToken = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\User\Helper\Data')
                ->generateResetPasswordLinkToken();
            $user->changeResetPasswordLinkToken($resetPasswordToken);
            $user->save();
        }

        $newDummyPassword = 'new_dummy_password2';

        $this->getRequest()
            ->setQuery('token', $resetPasswordToken)
            ->setQuery('id', $user->getId())
            ->setPost('password', $newDummyPassword)
            ->setPost('confirmation', 'invalid');

        $this->dispatch('backend/admin/auth/resetpasswordpost');

        $this->assertSessionMessages(
            $this->equalTo(array('Your password confirmation must match your password.')),
            \Magento\Core\Model\Message::ERROR
        );
        $this->assertRedirect();
    }
}
