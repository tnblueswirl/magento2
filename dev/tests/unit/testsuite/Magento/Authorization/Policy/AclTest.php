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
 * @package     unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Authorization\Policy;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Authorization\Policy\Acl
     */
    protected $_model;

    protected $_aclMock;

    protected $_aclBuilderMock;

    protected function setUp()
    {
        $this->_aclMock = $this->getMock('Magento\Acl');
        $this->_aclBuilderMock = $this->getMock('Magento\Acl\Builder', array(), array(), '', false);
        $this->_aclBuilderMock->expects($this->any())->method('getAcl')->will($this->returnValue($this->_aclMock));
        $this->_model = new \Magento\Authorization\Policy\Acl($this->_aclBuilderMock);
    }

    public function testIsAllowedReturnsTrueIfResourceIsAllowedToRole()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsFalseIfRoleDoesntExist()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new \Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed('some_role', 'some_resource'));
    }

    public function testIsAllowedReturnsTrueIfResourceDoesntExistAndAllResourcesAreNotPermitted()
    {
        $this->_aclMock->expects($this->at(0))
            ->method('isAllowed')
            ->with('some_role', 'some_resource')
            ->will($this->throwException(new \Zend_Acl_Role_Registry_Exception));

        $this->_aclMock->expects($this->once())
            ->method('has')
            ->with('some_resource')
            ->will($this->returnValue(false));

        $this->_aclMock->expects($this->at(2))
            ->method('isAllowed')
            ->with('some_role', null)
            ->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed('some_role', 'some_resource'));
    }
}
