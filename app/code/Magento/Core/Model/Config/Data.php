<?php
/**
 * Configuration data container for default, stores and websites config values
 *
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
namespace Magento\Core\Model\Config;

class Data implements \Magento\Core\Model\Config\DataInterface
{
    /**
     * Config data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Config source data
     *
     * @var array
     */
    protected $_source = array();

    /**
     * @param \Magento\Core\Model\Config\MetadataProcessor $processor
     * @param array $data
     */
    public function __construct(\Magento\Core\Model\Config\MetadataProcessor $processor, array $data)
    {
        $this->_data = $processor->process($data);
        $this->_source = $data;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Retrieve configuration value by path
     *
     * @param null|string $path
     * @return array|string
     */
    public function getValue($path = null)
    {
        if ($path === null) {
            return $this->_data;
        }
        $keys = explode('/', $path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }
        return $data;
    }

    /**
     * Set configuration value
     *l
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value)
    {
        $keys = explode('/', $path);
        $lastKey = array_pop($keys);
        $currentElement = &$this->_data;
        foreach ($keys as $key) {
            if (!isset($currentElement[$key])) {
                $currentElement[$key] = array();
            }
            $currentElement = &$currentElement[$key];
        }
        $currentElement[$lastKey] = $value;
    }
}
