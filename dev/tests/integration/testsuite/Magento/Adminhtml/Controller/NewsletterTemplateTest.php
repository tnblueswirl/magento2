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
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Adminhtml\Controller;

/**
 * @magentoAppArea adminhtml
 */
class NewsletterTemplateTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @var \Magento\Newsletter\Model\Template
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $post = array('code'=>'test data',
                      'subject'=>'test data2',
                      'sender_email'=>'sender@email.com',
                      'sender_name'=>'Test Sender Name',
                      'text'=>'Template Content');
        $this->getRequest()->setPost($post);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Template');
    }

    protected function tearDown()
    {
        /**
         * Unset messages
         */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Model\Session')
            ->getMessages(true);
        unset($this->_model);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSaveActionCreateNewTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/save');
        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::ERROR);
        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been saved.')), \Magento\Core\Model\Message::SUCCESS
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/newsletter_sample.php
     */
    public function testSaveActionEditTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been saved.')), \Magento\Core\Model\Message::SUCCESS
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testSaveActionTemplateWithInvalidDataAndVerifySuccessMessage()
    {
        $post = array('code'=>'test data',
                      'subject'=>'test data2',
                      'sender_email'=>'sender_email.com',
                      'sender_name'=>'Test Sender Name',
                      'text'=>'Template Content');
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/newsletter_template/save');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), \Magento\Core\Model\Message::ERROR);

        /**
         * Check that success message is not set
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::SUCCESS);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/newsletter_sample.php
     */
    public function testDeleteActionTemplateAndVerifySuccessMessage()
    {
        $this->_model->loadByCode('some_unique_code');
        $this->getRequest()->setParam('id', $this->_model->getId());
        $this->dispatch('backend/admin/newsletter_template/delete');

        /**
         * Check that errors was generated and set to session
         */
        $this->assertSessionMessages($this->isEmpty(), \Magento\Core\Model\Message::ERROR);

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The newsletter template has been deleted.')), \Magento\Core\Model\Message::SUCCESS
        );
    }
}
