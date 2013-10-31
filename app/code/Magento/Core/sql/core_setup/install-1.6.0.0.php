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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'core_resource'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_resource'))
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Resource Code')
    ->addColumn('version', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Resource Version')
    ->addColumn('data_version', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Data Version')
    ->setComment('Resources');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_website'))
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website Id')
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        ), 'Code')
    ->addColumn('name', \Magento\DB\Ddl\Table::TYPE_TEXT, 64, array(
        ), 'Website Name')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addColumn('default_group_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Default Group Id')
    ->addColumn('is_default', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Defines Is Website Default')
    ->addIndex($installer->getIdxName('core_website', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_website', array('sort_order')),
        array('sort_order'))
    ->addIndex($installer->getIdxName('core_website', array('default_group_id')),
        array('default_group_id'))
    ->setComment('Websites');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_store_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_store_group'))
    ->addColumn('group_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Group Id')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Store Group Name')
    ->addColumn('root_category_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Root Category Id')
    ->addColumn('default_store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Default Store Id')
    ->addIndex($installer->getIdxName('core_store_group', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('core_store_group', array('default_store_id')),
        array('default_store_id'))
    ->addForeignKey($installer->getFkName('core_store_group', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Store Groups');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_store'))
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store Id')
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        ), 'Code')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('group_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Group Id')
    ->addColumn('name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Store Name')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Sort Order')
    ->addColumn('is_active', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Activity')
    ->addIndex($installer->getIdxName('core_store', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_store', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('core_store', array('is_active', 'sort_order')),
        array('is_active', 'sort_order'))
    ->addIndex($installer->getIdxName('core_store', array('group_id')),
        array('group_id'))
    ->addForeignKey($installer->getFkName('core_store', 'group_id', 'core_store_group', 'group_id'),
        'group_id', $installer->getTable('core_store_group'), 'group_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('core_store', 'website_id', 'core_website', 'website_id'),
        'website_id', $installer->getTable('core_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Stores');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_config_data'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_config_data'))
    ->addColumn('config_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Config Id')
    ->addColumn('scope', \Magento\DB\Ddl\Table::TYPE_TEXT, 8, array(
        'nullable'  => false,
        'default'   => 'default',
        ), 'Config Scope')
    ->addColumn('scope_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Config Scope Id')
    ->addColumn('path', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => 'general',
        ), 'Config Path')
    ->addColumn('value', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(), 'Config Value')
    ->addIndex($installer->getIdxName('core_config_data', array('scope', 'scope_id', 'path'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('scope', 'scope_id', 'path'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->setComment('Config Data');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_email_template'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_email_template'))
    ->addColumn('template_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Template Id')
    ->addColumn('template_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 150, array(
        'nullable' => false
        ), 'Template Name')
    ->addColumn('template_text', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        'nullable' => false
        ), 'Template Content')
    ->addColumn('template_styles', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Templste Styles')
    ->addColumn('template_type', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Template Type')
    ->addColumn('template_subject', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        'nullable' => false,
        ), 'Template Subject')
    ->addColumn('template_sender_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        ), 'Template Sender Name')
    ->addColumn('template_sender_email', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        ), 'Template Sender Email')
    ->addColumn('added_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Date of Template Creation')
    ->addColumn('modified_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Date of Template Modification')
    ->addColumn('orig_template_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        ), 'Original Template Code')
    ->addColumn('orig_template_variables', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Original Template Variables')
    ->addIndex($installer->getIdxName('core_email_template', array('template_code'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('template_code'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_email_template', array('added_at')),
        array('added_at'))
    ->addIndex($installer->getIdxName('core_email_template', array('modified_at')),
        array('modified_at'))
    ->setComment('Email Templates');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_layout_update'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_layout_update'))
    ->addColumn('layout_update_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Layout Update Id')
    ->addColumn('handle', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Handle')
    ->addColumn('xml', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Xml')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('core_layout_update', array('handle')),
        array('handle'))
    ->setComment('Layout Updates');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_layout_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_layout_link'))
    ->addColumn('layout_link_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Link Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('area', \Magento\DB\Ddl\Table::TYPE_TEXT, 64, array(
        ), 'Area')
    ->addColumn('package', \Magento\DB\Ddl\Table::TYPE_TEXT, 64, array(
        ), 'Package')
    ->addColumn('theme', \Magento\DB\Ddl\Table::TYPE_TEXT, 64, array(
        ), 'Theme')
    ->addColumn('layout_update_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Layout Update Id')
    ->addIndex($installer->getIdxName('core_layout_link', array('store_id', 'package', 'theme', 'layout_update_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('store_id', 'package', 'theme', 'layout_update_id'),
        array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_layout_link', array('layout_update_id')),
        array('layout_update_id'))
    ->addForeignKey($installer->getFkName('core_layout_link', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('core_layout_link', 'layout_update_id', 'core_layout_update', 'layout_update_id'),
        'layout_update_id', $installer->getTable('core_layout_update'), 'layout_update_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Layout Link');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_session'))
    ->addColumn('session_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Session Id')
    ->addColumn('session_expires', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Date of Session Expiration')
    ->addColumn('session_data', \Magento\DB\Ddl\Table::TYPE_BLOB, '2M', array(
        'nullable'  => false,
        ), 'Session Data')
    ->setComment('Database Sessions Storage');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_translate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_translate'))
    ->addColumn('key_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Key Id of Translation')
    ->addColumn('string', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => \Magento\Core\Model\Translate::DEFAULT_STRING,
        ), 'Translation String')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('translate', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Translate')
    ->addColumn('locale', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        'nullable'  => false,
        'default'   => 'en_US',
        ), 'Locale')
    ->addIndex($installer->getIdxName('core_translate', array('store_id', 'locale', 'string'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('store_id', 'locale', 'string'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_translate', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('core_translate', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Translations');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_url_rewrite'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_url_rewrite'))
    ->addColumn('url_rewrite_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rewrite Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('id_path', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Id Path')
    ->addColumn('request_path', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Request Path')
    ->addColumn('target_path', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Target Path')
    ->addColumn('is_system', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '1',
        ), 'Defines is Rewrite System')
    ->addColumn('options', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Options')
    ->addColumn('description', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Deascription')
    ->addIndex($installer->getIdxName('core_url_rewrite', array('request_path', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('request_path', 'store_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_url_rewrite', array('id_path', 'is_system', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('id_path', 'is_system', 'store_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_url_rewrite', array('target_path', 'store_id')),
        array('target_path', 'store_id'))
    ->addIndex($installer->getIdxName('core_url_rewrite', array('id_path')),
        array('id_path'))
    ->addIndex($installer->getIdxName('core_url_rewrite', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('core_url_rewrite', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Url Rewrites');
$installer->getConnection()->createTable($table);

/**
 * Create table 'design_change'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('design_change'))
    ->addColumn('design_change_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Design Change Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('design', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Design')
    ->addColumn('date_from', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
        ), 'First Date of Design Activity')
    ->addColumn('date_to', \Magento\DB\Ddl\Table::TYPE_DATE, null, array(
        ), 'Last Date of Design Activity')
    ->addIndex($installer->getIdxName('design_change', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('design_change', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Design Changes');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_variable'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_variable'))
    ->addColumn('variable_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Variable Id')
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Variable Code')
    ->addColumn('name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Variable Name')
    ->addIndex($installer->getIdxName('core_variable', array('code'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('code'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->setComment('Variables');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_variable_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_variable_value'))
    ->addColumn('value_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Variable Value Id')
    ->addColumn('variable_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Variable Id')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('plain_value', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Plain Text Value')
    ->addColumn('html_value', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Html Value')
    ->addIndex($installer->getIdxName('core_variable_value', array('variable_id', 'store_id'),
        \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE),
        array('variable_id', 'store_id'), array('type' => \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('core_variable_value', array('variable_id')),
        array('variable_id'))
    ->addIndex($installer->getIdxName('core_variable_value', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('core_variable_value', 'store_id', 'core_store', 'store_id'),
        'store_id', $installer->getTable('core_store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('core_variable_value', 'variable_id', 'core_variable', 'variable_id'),
        'variable_id', $installer->getTable('core_variable'), 'variable_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Variable Value');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_cache'))
    ->addColumn('id', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Cache Id')
    ->addColumn('data', \Magento\DB\Ddl\Table::TYPE_BLOB, '2M', array(
        ), 'Cache Data')
    ->addColumn('create_time', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Cache Creation Time')
    ->addColumn('update_time', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Time of Cache Updating')
    ->addColumn('expire_time', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        ), 'Cache Expiration Time')
    ->addIndex($installer->getIdxName('core_cache', array('expire_time')),
        array('expire_time'))
    ->setComment('Caches');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache_tag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_cache_tag'))
    ->addColumn('tag', \Magento\DB\Ddl\Table::TYPE_TEXT, 100, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Tag')
    ->addColumn('cache_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 200, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Cache Id')
    ->addIndex($installer->getIdxName('core_cache_tag', array('cache_id')),
        array('cache_id'))
    ->setComment('Tag Caches');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_cache_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_cache_option'))
    ->addColumn('code', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Code')
    ->addColumn('value', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        ), 'Value')
    ->setComment('Cache Options');
$installer->getConnection()->createTable($table);

/**
 * Create table 'core_flag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_flag'))
    ->addColumn('flag_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Flag Id')
    ->addColumn('flag_code', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Flag Code')
    ->addColumn('state', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Flag State')
    ->addColumn('flag_data', \Magento\DB\Ddl\Table::TYPE_TEXT, '64k', array(
        ), 'Flag Data')
    ->addColumn('last_update', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default'   => \Magento\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
        ), 'Date of Last Flag Update')
    ->addIndex($installer->getIdxName('core_flag', array('last_update')),
        array('last_update'))
    ->setComment('Flag');
$installer->getConnection()->createTable($table);


/**
 * Insert core websites
 */
$installer->getConnection()->insertForce($installer->getTable('core_website'), array(
    'website_id'        => 0,
    'code'              => 'admin',
    'name'              => 'Admin',
    'sort_order'        => 0,
    'default_group_id'  => 0,
    'is_default'        => 0,
));
$installer->getConnection()->insertForce($installer->getTable('core_website'), array(
    'website_id'        => 1,
    'code'              => 'base',
    'name'              => 'Main Website',
    'sort_order'        => 0,
    'default_group_id'  => 1,
    'is_default'        => 1,
));

/**
 * Insert core store groups
 */
$installer->getConnection()->insertForce($installer->getTable('core_store_group'), array(
    'group_id'          => 0,
    'website_id'        => 0,
    'name'              => 'Default',
    'root_category_id'  => 0,
    'default_store_id'  => 0
));
$installer->getConnection()->insertForce($installer->getTable('core_store_group'), array(
    'group_id'          => 1,
    'website_id'        => 1,
    'name'              => 'Main Website Store',
    'root_category_id'  => 2,
    'default_store_id'  => 1
));

/**
 * Insert core stores
 */
$installer->getConnection()->insertForce($installer->getTable('core_store'), array(
    'store_id'      => 0,
    'code'          => 'admin',
    'website_id'    => 0,
    'group_id'      => 0,
    'name'          => 'Admin',
    'sort_order'    => 0,
    'is_active'     => 1,
));
$installer->getConnection()->insertForce($installer->getTable('core_store'), array(
    'store_id'      => 1,
    'code'          => 'default',
    'website_id'    => 1,
    'group_id'      => 1,
    'name'          => 'Default Store View',
    'sort_order'    => 0,
    'is_active'     => 1,
));

$installer->endSetup();

