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

namespace Magento\Catalog\Block\Layer;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture Magento/Catalog/_files/filterable_attributes.php
     */
    public function testGetFilters()
    {
        $currentCategory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Category');
        $currentCategory->load(3);

        /** @var $layer \Magento\Catalog\Model\Layer */
        $layer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Layer');
        $layer->setCurrentCategory($currentCategory);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Catalog\Block\Layer\View */
        $block = $layout->createBlock('Magento\Catalog\Block\Layer\View', 'block');

        $filters = $block->getFilters();

        $this->assertInternalType('array', $filters);
        $this->assertGreaterThan(3, count($filters)); // At minimum - category filter + 2 fixture attribute filters

        $found = false;
        foreach ($filters as $filter) {
            if ($filter instanceof \Magento\Catalog\Block\Layer\Filter\Category) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Category filter must be present');

        $attributeCodes = array('filterable_attribute_a', 'filterable_attribute_b');
        foreach ($attributeCodes as $attributeCode) {
            $found = false;
            foreach ($filters as $filter) {
                if (!($filter instanceof \Magento\Catalog\Block\Layer\Filter\Attribute)) {
                    continue;
                }
                if ($attributeCode == $filter->getAttributeModel()->getAttributeCode()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, "Filter for attribute {$attributeCode} must be present");
        }
    }
}
