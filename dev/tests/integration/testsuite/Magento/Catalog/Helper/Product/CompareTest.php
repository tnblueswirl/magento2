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

class CompareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper =
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Helper\Product\Compare');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testGetListUrl()
    {
        /** @var $empty \Magento\Catalog\Helper\Product\Compare */
        $empty = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Helper\Product\Compare');
        $this->assertContains('/catalog/product_compare/index/', $empty->getListUrl());

        $this->_populateCompareList();
        $this->assertRegExp('#/catalog/product_compare/index/items/(?:10,11|11,10)/#', $this->_helper->getListUrl());
    }

    public function testGetAddUrl()
    {
        $this->_testGetProductUrl('getAddUrl', '/catalog/product_compare/add/');
    }

    public function testGetAddToWishlistUrl()
    {
        $this->_testGetProductUrl('getAddToWishlistUrl', '/wishlist/index/add/');
    }

    public function testGetAddToCartUrl()
    {
        $this->_testGetProductUrl('getAddToCartUrl', '/checkout/cart/add/');
    }

    public function testGetRemoveUrl()
    {
        $this->_testGetProductUrl('getRemoveUrl', '/catalog/product_compare/remove/');
    }

    public function testGetClearListUrl()
    {
        $this->assertContains('/catalog/product_compare/clear/', $this->_helper->getClearListUrl());
    }

    /**
     * @see testGetListUrl() for coverage of customer case
     */
    public function testGetItemCollection()
    {
        $this->assertInstanceOf(
            'Magento\Catalog\Model\Resource\Product\Compare\Item\Collection', $this->_helper->getItemCollection()
        );
    }

    /**
     * calculate()
     * getItemCount()
     * hasItems()
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testCalculate()
    {
         /** @var $session \Magento\Catalog\Model\Session */
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Session');
        try {
            $session->unsCatalogCompareItemsCount();
            $this->assertFalse($this->_helper->hasItems());
            $this->assertEquals(0, $session->getCatalogCompareItemsCount());

            $this->_populateCompareList();
            $this->_helper->calculate();
            $this->assertEquals(2, $session->getCatalogCompareItemsCount());
            $this->assertTrue($this->_helper->hasItems());

            $session->unsCatalogCompareItemsCount();
        } catch (\Exception $e) {
            $session->unsCatalogCompareItemsCount();
            throw $e;
        }
    }

    public function testSetGetAllowUsedFlat()
    {
        $this->assertTrue($this->_helper->getAllowUsedFlat());
        $this->_helper->setAllowUsedFlat(false);
        $this->assertFalse($this->_helper->getAllowUsedFlat());
    }

    protected function _testGetProductUrl($method, $expectedFullAction)
    {
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->setId(10);
        $url = $this->_helper->$method($product);
        $this->assertContains($expectedFullAction, $url);
        $this->assertContains('/product/10/', $url);
        $this->assertContains('/uenc/', $url);
    }

    /**
     * Add products from fixture to compare list
     */
    protected function _populateCompareList()
    {
        $productOne = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $productTwo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $productOne->load(10);
        $productTwo->load(11);
        /** @var $compareList \Magento\Catalog\Model\Product\Compare\ListCompare */
        $compareList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product\Compare\ListCompare');
        $compareList->addProduct($productOne)->addProduct($productTwo);
    }
}
