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
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Backend\Block\System\Config;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Tabs
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Factory\Helper
     */
    protected $_helperFactoryMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $this->_requestMock->expects($this->any())->method('getParam')->with('section')
            ->will($this->returnValue('currentSectionId'));
        $this->_structureMock = $this->getMock('Magento\Backend\Model\Config\Structure', array(), array(), '', false);
        $this->_structureMock->expects($this->once())->method('getTabs')->will($this->returnValue(array()));
        $this->_urlBuilderMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);

        $this->_helperFactoryMock = $this->getMock(
            'Magento\Core\Model\Factory\Helper', array('get'), array(), '', false, false
        );
        $backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $this->_helperFactoryMock
            ->expects($this->once())
            ->method('get')
            ->with('Magento\Backend\Helper\Data', array())
            ->will($this->returnValue($backendHelperMock));

        $data = array(
            'configStructure' => $this->_structureMock,
            'request' => $this->_requestMock,
            'urlBuilder' => $this->_urlBuilderMock,
            'helperFactory' => $this->_helperFactoryMock,
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $helper->getObject('Magento\Backend\Block\System\Config\Tabs', $data);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_requestMock);
        unset($this->_structureMock);
        unset($this->_urlBuilderMock);
    }

    public function testGetSectionUrl()
    {
        $this->_urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', array('_current' => true, 'section' => 'testSectionId'))
            ->will($this->returnValue('testSectionUrl'));

        $sectionMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('testSectionId'));

        $this->assertEquals('testSectionUrl', $this->_object->getSectionUrl($sectionMock));
    }

    public function testIsSectionActiveReturnsTrueForActiveSection()
    {
        $sectionMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('currentSectionId'));
        $this->assertTrue($this->_object->isSectionActive($sectionMock));
    }

    public function testIsSectionActiveReturnsFalseForNonActiveSection()
    {
        $sectionMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('nonCurrentSectionId'));
        $this->assertFalse($this->_object->isSectionActive($sectionMock));
    }
}
