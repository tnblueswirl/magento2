<?php
/**
 * Local Application configuration loader (app/etc/local.xml)
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
namespace Magento\Core\Model\Config\Loader;

class Local
{
    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Directory registry
     *
     * @var string
     */
    protected $_dirs;

    /**
     * Custom config file
     *
     * @var string
     */
    protected $_customFile;

    /**
     * Custom configuration string
     *
     * @var string
     */
    protected $_customConfig;

    /**
     * Configuration identifier attributes
     *
     * @var array
     */
    protected $_idAttributes = array('/config/resource' => 'name', '/config/connection' => 'name');

    /**
     * @param string $configDirectory
     * @param string $customConfig
     * @param string $customFile
     */
    public function __construct($configDirectory, $customConfig = null, $customFile = null)
    {
        $this->_dir = $configDirectory;
        $this->_customFile = $customFile;
        $this->_customConfig = $customConfig;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function load()
    {
        $localConfig = new \Magento\Config\Dom('<config/>', $this->_idAttributes);

        $localConfigFile = $this->_dir . DIRECTORY_SEPARATOR . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig->merge(file_get_contents($localConfigFile));

            // 2. app/etc/<dir>/<file>.xml
            if (preg_match('/^[a-z\d_-]+(\/|\\\)+[a-z\d_-]+\.xml$/', $this->_customFile)) {
                $localConfigExtraFile = $this->_dir . DIRECTORY_SEPARATOR . $this->_customFile;
                $localConfig->merge(file_get_contents($localConfigExtraFile));
            }
        }

        // 3. extra local configuration string
        if ($this->_customConfig) {
            $localConfig->merge($this->_customConfig);
        }
        $converter = new \Magento\Config\Converter\Dom\Flat($this->_idAttributes);

        $result = $converter->convert($localConfig->getDom());
        return !empty($result['config']) ? $result['config'] : array();
    }
}
