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
 * @category   Magento
 * @package    Tools
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Magento\Tools\Migration\System\Configuration;

class Generator
{
    /**
     * @var \Magento\Tools\Migration\System\FileManager
     */
    protected $_fileManager;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\Formatter
     */
    protected $_xmlFormatter;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\AbstractLogger
     */
    protected $_logger;

    /**
     * Base directory path
     *
     * @var string
     */
    protected $_basePath;

    /**
     * @var \Magento\Tools\Migration\System\Configuration\AbstractLogger
     */
    protected $_fileSchemaPath;

    public function __construct(
        \Magento\Tools\Migration\System\Configuration\Formatter $xmlFormatter,
        \Magento\Tools\Migration\System\FileManager $fileManager,
        \Magento\Tools\Migration\System\Configuration\AbstractLogger $logger
    ) {
        $this->_fileManager = $fileManager;
        $this->_xmlFormatter = $xmlFormatter;
        $this->_logger = $logger;


        $this->_basePath = realpath(__DIR__ . '/../../../../../../../');
        $this->_fileSchemaPath = $this->_basePath
            . '/app/code/Mage/Backend/etc/system_file.xsd';
    }

    /**
     * Create configuration array from xml file
     *
     * @param string $fileName
     * @param array $configuration
     */
    public function createConfiguration($fileName, array $configuration)
    {
        $domDocument = $this->_createDOMDocument($configuration);
        if (@!$domDocument->schemaValidate($this->_fileSchemaPath)) {
            $this->_logger->add(
                $this->_removeBasePath($fileName),
                \Magento\Tools\Migration\System\Configuration\AbstractLogger::FILE_KEY_INVALID
            );
        } else {
            $this->_logger->add(
                $this->_removeBasePath($fileName),
                \Magento\Tools\Migration\System\Configuration\AbstractLogger::FILE_KEY_VALID
            );
        }

        $output = $this->_xmlFormatter->parseString($domDocument->saveXml(), array(
            'indent' => true,
            'input-xml' => true,
            'output-xml' => true,
            'add-xml-space' => false,
            'indent-spaces' => 4,
            'wrap' => 1000
        ));
        $newFileName = $this->_getPathToSave($fileName);
        $this->_fileManager->write($newFileName, $output);
    }

    /**
     *  Create DOM document based on configuration
     *
     * @param array $configuration
     * @return \DOMDocument
     */
    protected function _createDOMDocument(array $configuration)
    {
        $dom = new \DOMDocument();
        $configElement = $dom->createElement('config');
        $systemElement = $dom->createElement('system');
        $configElement->appendChild($systemElement);
        $dom->appendChild($configElement);

        foreach ($configuration['nodes'] as $config) {
            $element = $this->_createElement($config, $dom);
            $systemElement->appendChild($element);
        }
        return $dom;
    }

    /**
     * Create element
     *
     * @param array $config
     * @param \DOMDocument $dom
     * @return \DOMElement
     */
    protected function _createElement($config, \DOMDocument $dom)
    {
        $element = $dom->createElement($this->_getValue($config, 'nodeName'), $this->_getValue($config, '#text', ''));
        if ($this->_getValue($config, '#cdata-section')) {
            $cdataSection = $dom->createCDATASection($this->_getValue($config, '#cdata-section', ''));
            $element->appendChild($cdataSection);
        }

        foreach ($this->_getValue($config, '@attributes', array()) as $attributeName => $attributeValue) {
            $element->setAttribute($attributeName, $attributeValue);
        }

        foreach ($this->_getValue($config, 'parameters', array()) as $paramConfig) {
            if ($this->_getValue($paramConfig, 'name') == '#text') {
                $element->nodeValue = $this->_getValue($paramConfig, 'value', '');
                continue;
            }

            $childElement = $dom->createElement($paramConfig['name'], $this->_getValue($paramConfig, '#text', ''));

            if ($this->_getValue($paramConfig, '#cdata-section')) {
                $childCDataSection = $dom->createCDATASection($this->_getValue($paramConfig, '#cdata-section'));
                $childElement->appendChild($childCDataSection);
            }

            foreach ($this->_getValue($paramConfig, '@attributes', array()) as $attributeName => $attributeValue) {
                $childElement->setAttribute($attributeName, $attributeValue);
            }

            foreach ($this->_getValue($paramConfig, 'subConfig', array()) as $subConfig) {
                $childElement->appendChild($this->_createElement($subConfig, $dom));
            }

            $element->appendChild($childElement);
        }

        foreach ($this->_getValue($config, 'subConfig', array()) as $subConfig) {
            $element->appendChild($this->_createElement($subConfig, $dom));
        }

        return $element;
    }

    /**
     * Get value from array by key
     *
     * @param array $source
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($source, $key, $default = null)
    {
        return array_key_exists($key, $source) ? $source[$key] : $default;
    }

    /**
     * Get new path to system configuration file
     *
     * @param $fileName
     * @return string
     */
    protected function _getPathToSave($fileName)
    {
        return dirname($fileName) . DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';
    }

    /**
     * Remove path to magento application
     *
     * @param $filename
     * @return string
     */
    protected function _removeBasePath($filename)
    {
        return str_replace($this->_basePath . DIRECTORY_SEPARATOR, '', $filename);
    }
}
