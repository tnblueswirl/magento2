<?php
/**
 * \Magento\Payment\Model\Config\Reader
 *
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
namespace Magento\Payment\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Payment\Model\Config\Reader
     */
    protected $_model;

    /** @var  \Magento\Config\FileResolverInterface/PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $cache \Magento\Core\Model\Cache */
        $cache = $objectManager->create('Magento\Core\Model\Cache');
        $cache->clean();
        $this->_fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = $objectManager->create('Magento\Payment\Model\Config\Reader',
            array('fileResolver'=>$this->_fileResolverMock));
    }

    public function testRead()
    {
        $fileList = array(__DIR__ . '/../_files/payment.xml');
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));
        $result = $this->_model->read('global');
        $expected = array(
            'credit_cards' => array(
                'SO' => 'Solo',
                'SM' => 'Switch/Maestro',
            ),
            'groups' => array(
                'paypal' => 'PayPal'
            ),
        );
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/../_files/payment.xml',
            __DIR__ . '/../_files/payment2.xml'
        );
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));

        $result = $this->_model->read('global');
        $expected = array(
            'credit_cards' => array(
                'AE' => 'American Express',
                'SM' => 'Switch/Maestro',
                'SO' => 'Solo',
            ),
            'groups' => array(
                'paypal' => 'PayPal Payment Methods',
                'offline' => 'Offline Payment Methods',
            ),
        );
        $this->assertEquals($expected, $result);
    }
}