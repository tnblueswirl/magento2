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
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Catalog\Helper\Product;

class FlatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Helper\Product\Flat');
    }

    public function testGetFlag()
    {
        $flag = $this->_helper->getFlag();
        $this->assertInstanceOf('Magento\Catalog\Model\Product\Flat\Flag', $flag);
    }

    public function testIsBuilt()
    {
        $this->assertFalse($this->_helper->isBuilt());
        $flag = $this->_helper->getFlag();
        try {
            $flag->setIsBuilt(true);
            $this->assertTrue($this->_helper->isBuilt());

            $flag->setIsBuilt(false);
        } catch (\Exception $e) {
            $flag->setIsBuilt(false);
            throw $e;
        }
    }

    public function testIsEnabledDefault()
    {

        $this->assertFalse($this->_helper->isEnabled());
    }

    /**
     * @magentoConfigFixture current_store catalog/frontend/flat_catalog_product 1
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->_helper->isEnabled());
    }

    public function testIsAddFilterableAttributesDefault()
    {
        $this->assertEquals(0, $this->_helper->isAddFilterableAttributes());
    }

    public function testIsAddFilterableAttributes()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Helper\Product\Flat', array('addFilterableAttrs' => 1));
        $this->assertEquals(1, $helper->isAddFilterableAttributes());
    }

    public function testIsAddChildDataDefault()
    {
        $this->assertEquals(0, $this->_helper->isAddChildData());
    }

    public function testIsAddChildData()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Helper\Product\Flat', array('addChildData' => 1));
        $this->assertEquals(1, $helper->isAddChildData());
    }
}
