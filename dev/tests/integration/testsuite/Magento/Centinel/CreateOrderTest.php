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
 * @package     Magento_Centinel
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Centinel;

/**
 * @magentoAppArea adminhtml
 */
class CreateOrderTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoConfigFixture admin_store payment/ccsave/centinel 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order \Magento\Adminhtml\Model\Sales\Order\Create */
        $order = $this->_objectManager->get('Magento\Adminhtml\Model\Sales\Order\Create');
        $paymentData = array(
            'cc_owner' => 'Test User',
            'cc_type' => 'visa',
            'cc_number' => '4111111111111111',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2013',
            'cc_cid' => '123',
            'method' => 'ccsave',
        );
        $order->addProducts(array(1 => array('qty' => 1)))->getQuote()->getPayment()->addData($paymentData);
        $this->dispatch('backend/admin/sales_order_create/index');
        $this->assertContains('<div class="centinel">', $this->getResponse()->getBody());
    }
}
