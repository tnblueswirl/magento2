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
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
$table = $installer->getTable('core_translate');

$connection->dropIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'string'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
));

$connection->addColumn($table, 'crc_string', array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_BIGINT,
    'nullable' => false,
    'default'  => crc32(\Magento\Core\Model\Translate::DEFAULT_STRING),
    'comment'  => 'Translation String CRC32 Hash',
));

$connection->addIndex($table, $installer->getIdxName(
    'core_translate',
    array('store_id', 'locale', 'crc_string', 'string'),
    \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
), array('store_id', 'locale', 'crc_string', 'string'), \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
