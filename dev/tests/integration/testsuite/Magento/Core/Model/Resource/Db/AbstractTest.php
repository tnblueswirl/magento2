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
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Core\Model\Resource\Db;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected $_model;

    protected function setUp()
    {
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource');
        $this->_model = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb',
            array('resource' => $resource)
        );
    }


    public function testConstruct()
    {
        $resourceProperty = new \ReflectionProperty(get_class($this->_model), '_resources');
        $resourceProperty->setAccessible(true);
        $this->assertInstanceOf('Magento\Core\Model\Resource', $resourceProperty->getValue($this->_model));
    }

    public function testSetMainTable()
    {
        $setMainTableMethod = new \ReflectionMethod($this->_model, '_setMainTable');
        $setMainTableMethod->setAccessible(true);

        $tableName = $this->_model->getTable('core_website');
        $idFieldName = 'website_id';

        $setMainTableMethod->invoke($this->_model, $tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $setMainTableMethod->invoke($this->_model, $tableName, $idFieldName);
        $this->assertEquals($tableName, $this->_model->getMainTable());
        $this->assertEquals($idFieldName, $this->_model->getIdFieldName());
    }


    public function testGetTableName()
    {
        $tableNameOrig = 'core_website';
        $tableSuffix = 'suffix';
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Resource', array('tablePrefix' => 'prefix_')
        );

        $model = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb',
            array('resource' => $resource)
        );

        $tableName = $model->getTable(array($tableNameOrig, $tableSuffix));
        $this->assertEquals('prefix_core_website_suffix', $tableName);
    }
}
