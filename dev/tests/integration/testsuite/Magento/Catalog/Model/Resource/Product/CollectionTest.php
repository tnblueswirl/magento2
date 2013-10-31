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

namespace Magento\Catalog\Model\Resource\Product;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_collection;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product\Collection');
    }

    /**
     * @dataProvider setOrderDataProvider
     */
    public function testSetOrder($order, $expectedOrder)
    {
        $this->_collection->setOrder($order);
        $this->_collection->load(); // perform real SQL query

        $selectOrder = $this->_collection->getSelect()->getPart(\Zend_Db_Select::ORDER);
        foreach ($expectedOrder as $field) {
            $orderBy = array_shift($selectOrder);
            $this->assertArrayHasKey(0, $orderBy);
            $this->assertTrue(false !== strpos($orderBy[0], $field),
                'Ordering by same column more than once is restricted by multiple RDBMS requirements.'
            );
        }
    }

    public function setOrderDataProvider()
    {
        return array(
            array(array('sku', 'sku'), array('sku')),
            array(array('sku', 'name', 'sku'), array('name', 'sku')),
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/Model/Resource/_files/url_rewrites.php
     * @magentoConfigFixture current_store catalog/seo/product_use_categories 1
     */
    public function testAddUrlRewrite()
    {
        $this->_collection->addUrlRewrite(3);
        $expectedResult = array(
            'category-1/url-key.html',
            'category-1/url-key-1.html',
            'category-1/url-key-2.html',
            'category-1/url-key-5.html',
            'category-1/url-key-1000.html',
            'category-1/url-key-999.html',
            'category-1/url-key-asdf.html',
        );
        $this->assertEquals($expectedResult, $this->_collection->getColumnValues('request_path'));
    }
}
