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

/**
 * Test class for \Magento\Paypal\Helper\Checkout
 */
namespace Magento\Paypal\Helper;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Model\Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\QuoteFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Paypal\Helper\Checkout
     */
    protected $_checkout;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_session = $this->getMockBuilder('Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('getLastRealOrder', 'replaceQuote', 'unsLastRealOrderId'))
            ->getMock();
        $this->_quoteFactory = $this->getMockBuilder('Magento\Sales\Model\QuoteFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();

        $this->_checkout = new \Magento\Paypal\Helper\Checkout($this->_session, $this->_quoteFactory);
    }

    /**
     * Get order mock
     *
     * @param bool $hasOrderId
     * @param array $mockMethods
     * @return \Magento\Sales\Model\Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOrderMock($hasOrderId, $mockMethods = array())
    {
        $order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(array_merge(array('getId'), $mockMethods))
            ->getMock();
        $order->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($hasOrderId ? 'order id' : null));
        return $order;
    }

    /**
     * @param bool $hasOrderId
     * @param bool $isOrderCancelled
     * @param bool $expectedResult
     * @dataProvider cancelCurrentOrderDataProvider
     */
    public function testCancelCurrentOrder($hasOrderId, $isOrderCancelled, $expectedResult)
    {
        $comment = 'Some test comment';
        $order = $this->_getOrderMock($hasOrderId, array('registerCancellation', 'save'));
        $order->setData('state', $isOrderCancelled ? \Magento\Sales\Model\Order::STATE_CANCELED : 'some another state');
        if ($expectedResult) {
            $order->expects($this->once())
                ->method('registerCancellation')
                ->with($this->equalTo($comment))
                ->will($this->returnSelf());
            $order->expects($this->once())
                ->method('save');
        } else {
            $order->expects($this->never())
                ->method('registerCancellation');
            $order->expects($this->never())
                ->method('save');
        }

        $this->_session->expects($this->any())
            ->method('getLastRealOrder')
            ->will($this->returnValue($order));
        $this->assertEquals($expectedResult, $this->_checkout->cancelCurrentOrder($comment));
    }

    /**
     * @return array
     */
    public function cancelCurrentOrderDataProvider()
    {
        return array(
            array(true, false, true),
            array(true, true, false),
            array(false, true, false),
            array(false, false, false),
        );
    }

    /**
     * @param bool $hasOrderId
     * @param bool $hasQuoteId
     * @dataProvider restoreQuoteDataProvider
     */
    public function testRestoreQuote($hasOrderId, $hasQuoteId)
    {
        $quote = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'save', 'setIsActive', 'setReservedOrderId', 'load'))
            ->getMock();
        $order = $this->_getOrderMock($hasOrderId);
        $this->_session->expects($this->once())
            ->method('getLastRealOrder')
            ->will($this->returnValue($order));

        if ($hasOrderId) {
            $quoteId = 'quote id';
            $order->setQuoteId($quoteId);
            $this->_quoteFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($quote));
            $quote->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($hasQuoteId ? 'some quote id' : null));
            $quote->expects($this->any())
                ->method('load')
                ->with($this->equalTo($quoteId))
                ->will($this->returnValue($quote));
            if ($hasQuoteId) {
                $quote->expects($this->once())
                    ->method('setIsActive')
                    ->with($this->equalTo(1))
                    ->will($this->returnSelf());
                $quote->expects($this->once())
                    ->method('setReservedOrderId')
                    ->with($this->isNull())
                    ->will($this->returnSelf());
                $quote->expects($this->once())
                    ->method('save');
                $this->_session->expects($this->once())
                    ->method('replaceQuote')
                    ->with($quote)
                    ->will($this->returnSelf());
            } else {
                $quote->expects($this->never())
                    ->method('setIsActive');
                $quote->expects($this->never())
                    ->method('setReservedOrderId');
                $quote->expects($this->never())
                    ->method('save');
            }
        }
        if ($hasOrderId && $hasQuoteId) {
            $this->_session->expects($this->once())
                ->method('unsLastRealOrderId');
        } else {
            $this->_session->expects($this->never())
                ->method('replaceQuote');
            $this->_session->expects($this->never())
                ->method('unsLastRealOrderId');
        }
        $result = $this->_checkout->restoreQuote();
        $this->assertEquals($result, $hasOrderId && $hasQuoteId);
    }

    /**
     * @return array
     */
    public function restoreQuoteDataProvider()
    {
        return array(
            array(true, true),
            array(true, false),
            array(false, true),
            array(false, false),
        );
    }
}
