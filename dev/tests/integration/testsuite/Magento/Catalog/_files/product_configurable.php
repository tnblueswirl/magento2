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

/* Create attribute */
/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'));
/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Resource\Eav\Attribute');
$attribute->setData(array(
    'attribute_code'                => 'test_configurable',
    'entity_type_id'                => $installer->getEntityTypeId('catalog_product'),
    'is_global'                     => 1,
    'is_user_defined'               => 1,
    'frontend_input'                => 'select',
    'is_unique'                     => 0,
    'is_required'                   => 1,
    'is_configurable'               => 1,
    'is_searchable'                 => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable'                 => 0,
    'is_filterable'                 => 0,
    'is_filterable_in_search'       => 0,
    'is_used_for_promo_rules'       => 0,
    'is_html_allowed_on_front'      => 1,
    'is_visible_on_front'           => 0,
    'used_in_product_listing'       => 0,
    'used_for_sort_by'              => 0,
    'frontend_label'                => array('Test Configurable'),
    'backend_type'                  => 'int',
    'option'                        => array(
        'value' => array(
            'option_0' => array('Option 1'),
            'option_1' => array('Option 2'),
        ),
        'order' => array(
            'option_0' => 1,
            'option_1' => 2,
        )
    ),
));
$attribute->save();

/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());

/* Create simple products per each option */
/** @var $options \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection */
$options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection');
$options->setAttributeFilter($attribute->getId());

$attributeValues = array();
$productIds = array();
foreach ($options as $option) {
    /** @var $product \Magento\Catalog\Model\Product */
    $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setId($option->getId() * 10)
        ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
        ->setWebsiteIds(array(1))
        ->setName('Configurable Option' . $option->getId())
        ->setSku('simple_' . $option->getId())
        ->setPrice(10)
        ->setTestConfigurable($option->getId())
        ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
        ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
        ->setStockData(array(
            'use_config_manage_stock' => 1,
            'qty'                     => 100,
            'is_qty_decimal'          => 0,
            'is_in_stock'             => 1,
        ))
        ->save();
    $attributeValues[] = array(
        'label'         => 'test',
        'attribute_id'  => $attribute->getId(),
        'value_index'   => $option->getId(),
        'is_percent'    => false,
        'pricing_value' => 5,
    );
    $productIds[] = $product->getId();
}

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE)
    ->setId(1)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds(array(1))
    ->setName('Configurable Product')
    ->setSku('configurable')
    ->setPrice(100)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->setStockData(array(
        'use_config_manage_stock' => 1,
        'is_in_stock'             => 1,
    ))
    ->setAssociatedProductIds($productIds)
    ->setConfigurableAttributesData(array(array(
        'attribute_id'   => $attribute->getId(),
        'attribute_code' => $attribute->getAttributeCode(),
        'frontend_label' => 'test',
        'values'         => $attributeValues,
    )))
    ->save();
