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
namespace Magento\Core\Model\Locale\Hierarchy\Config;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver
     */
    protected $_model;

    /**
     * @var \Magento\App\Dir
     */
    protected $_appDirsMock;

    protected function setUp()
    {
        $this->_appDirsMock = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $this->_model = new \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver($this->_appDirsMock);
    }

    /**
     * @covers \Magento\Core\Model\Locale\Hierarchy\Config\FileResolver::get
     */
    public function testGet()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        $this->_appDirsMock->expects($this->once())
            ->method('getDir')
            ->with(\Magento\App\Dir::LOCALE)
            ->will($this->returnValue($path));

        $expectedFilesList = array(
            $path . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml',
            $path . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'hierarchy_config.xml'
        );

        $this->assertEquals($expectedFilesList, $this->_model->get('hierarchy_config.xml', 'scope'));
    }
}
