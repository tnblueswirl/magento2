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


namespace Magento\Adminhtml\Controller\Sales\Order;

/**
 * @magentoAppArea adminhtml
 */
class CreateTest extends \Magento\Backend\Utility\Controller
{
    public function testLoadBlockAction()
    {
        $this->getRequest()->setParam('block', ',');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $this->assertEquals('{"message":""}', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionData()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Adminhtml\Model\Sales\Order\Create')
            ->addProducts(array(1 => array('qty' => 1)));
        $this->getRequest()->setParam('block', 'data');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id=\"sales_order_create_search_grid\">', $html);
        $this->assertContains('<div id=\"order-billing_method_form\">', $html);
        $this->assertContains('id=\"shipping-method-overlay\"', $html);
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @dataProvider loadBlockActionsDataProvider
     */
    public function testLoadBlockActions($block, $expected)
    {
        $this->getRequest()->setParam('block', $block);
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains($expected, $html);
    }

    public function loadBlockActionsDataProvider()
    {
        return array(
            'shipping_method' => array('shipping_method', 'id=\"shipping-method-overlay\"'),
            'billing_method' => array('billing_method', '<div id=\"order-billing_method_form\">'),
            'newsletter' => array('newsletter', 'name=\"newsletter:subscribe\"'),
            'search' => array('search', '<div id=\"sales_order_create_search_grid\">'),
            'search_grid' => array('search', '<div id=\"sales_order_create_search_grid\">'),
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testLoadBlockActionItems()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Adminhtml\Model\Sales\Order\Create')
            ->addProducts(array(1 => array('qty' => 1)));
        $this->getRequest()->setParam('block', 'items');
        $this->getRequest()->setParam('json', 1);
        $this->dispatch('backend/admin/sales_order_create/loadBlock');
        $html = $this->getResponse()->getBody();
        $this->assertContains('id=\"coupons:code\"', $html);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order \Magento\Adminhtml\Model\Sales\Order\Create */
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Adminhtml\Model\Sales\Order\Create');
        $order->addProducts(array(1 => array('qty' => 1)));
        $this->dispatch('backend/admin/sales_order_create/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div id="order-customer-selector"', $html);
        $this->assertContains('<div id="sales_order_create_customer_grid">', $html);
        $this->assertContains('<div id="order-billing_method_form">', $html);
        $this->assertContains('id="shipping-method-overlay"', $html);
        $this->assertContains('<div id="sales_order_create_search_grid">', $html);
        $this->assertContains('id="coupons:code"', $html);
    }
}
