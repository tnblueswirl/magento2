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
namespace Magento\Core\Model\Resource\Type\Db\Pdo;

class Mysql extends \Magento\Core\Model\Resource\Type\Db
    implements \Magento\Core\Model\Resource\ConnectionAdapterInterface
{
    /**
     * Dirs instance
     *
     * @var \Magento\App\Dir
     */
    protected $_dirs;

    /**
     * @var array
     */
    protected $_connectionConfig;

    /**
     * @var string
     */
    protected $_initStatements;

    /**
     * @var boolean
     */
    protected $_isActive;

    /**
     * @param \Magento\App\Dir $dirs
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbName
     * @param array $profiler
     * @param string $initStatements
     * @param string $type
     * @param bool $active
     */
    public function __construct(
        \Magento\App\Dir $dirs,
        $host,
        $username,
        $password,
        $dbName,
        array $profiler = array(),
        $initStatements = 'SET NAMES utf8',
        $type = 'pdo_mysql',
        $active = false
    ) {
        $this->_dirs = $dirs;
        $this->_connectionConfig = array(
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'dbname' => $dbName,
            'type' => $type,
            'profiler' => !empty($profiler) && $profiler !== 'false'
        );

        $this->_host = $host;
        $this->_type = $type;
        $this->_initStatements = $initStatements;
        $this->_isActive = !($active === 'false' || $active === '0');
        parent::__construct();
    }

    /**
     * Get connection
     *
     * @return \Magento\DB\Adapter\AdapterInterface|null
     */
    public function getConnection()
    {
        if (!$this->_isActive) {
            return null;
        }

        $connection = $this->_getDbAdapterInstance();
        if (!empty($this->_initStatements) && $connection) {
            $connection->query($this->_initStatements);
        }

        $profiler = $connection->getProfiler();
        if ($profiler instanceof \Magento\DB\Profiler) {
            $profiler->setType($this->_type);
            $profiler->setHost($this->_host);
        }

        return $connection;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @return \Magento\DB\Adapter\Pdo\Mysql
     */
    protected function _getDbAdapterInstance()
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($this->_dirs, $this->_connectionConfig);
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento\DB\Adapter\Pdo\Mysql';
    }
}
