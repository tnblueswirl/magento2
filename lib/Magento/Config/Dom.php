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
 * @package     Framework
 * @subpackage  Config
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento configuration XML DOM utility
 */
namespace Magento\Config;

class Dom
{
    /**
     * Prefix which will be used for root namespace
     */
    const ROOT_NAMESPACE_PREFIX = 'x';

    /**
     * Format of items in errors array to be used by default. Available placeholders - fields of \LibXMLError.
     */
    const ERROR_FORMAT_DEFAULT = "%message%\nLine: %line%\n";

    /**
     * Dom document
     *
     * @var \DOMDocument
     */
    protected $_dom;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes;

    /**
     * Schema validation file
     *
     * @var string
     */
    protected $_schemaFile;

    /**
     * Format of error messages
     *
     * @var string
     */
    protected $_errorFormat;

    /**
     * Default namespace for xml elements
     *
     * @var string
     */
    protected $_rootNamespace;

    /**
     * Build DOM with initial XML contents and specifying identifier attributes for merging
     *
     * Format of $idAttributes: array('/xpath/to/some/node' => 'id_attribute_name')
     * The path to ID attribute name should not include any attribute notations or modifiers -- only node names
     *
     * @param string $xml
     * @param array $idAttributes
     * @param string $schemaFile
     * @param string $errorFormat
     * @throws \Magento\Config\Dom\ValidationException
     */
    public function __construct(
        $xml, array $idAttributes = array(), $schemaFile = null, $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        $this->_schemaFile    = $schemaFile;
        $this->_idAttributes  = $idAttributes;
        $this->_errorFormat   = $errorFormat;
        $this->_dom           = $this->_initDom($xml);
        $this->_rootNamespace = $this->_dom->lookupNamespaceUri($this->_dom->namespaceURI);
    }

    /**
     * Merge $xml into DOM document
     *
     * @param string $xml
     * @return void
     * @throws \Magento\Config\Dom\ValidationException
     */
    public function merge($xml)
    {
        $dom = $this->_initDom($xml);
        $this->_mergeNode($dom->documentElement, '');
    }

    /**
     * Recursive merging of the \DOMElement into the original document
     *
     * Algorithm:
     * 1. Find the same node in original document
     * 2. Extend and override original document node attributes and scalar value if found
     * 3. Append new node if original document doesn't have the same node
     *
     * @param \DOMElement $node
     * @param string $parentPath path to parent node
     */
    protected function _mergeNode(\DOMElement $node, $parentPath)
    {
        $path = $this->_getNodePathByParent($node, $parentPath);

        $matchedNode = $this->_getMatchedNode($path);

        /* Update matched node attributes and value */
        if ($matchedNode) {
            $this->_mergeAttributes($matchedNode, $node);
            if (!$node->hasChildNodes()) {
                return;
            }
            /* override node value */
            if ($this->_isTextNode($node)) {
                /* skip the case when the matched node has children, otherwise they get overriden */
                if (!$matchedNode->hasChildNodes() || $this->_isTextNode($matchedNode)) {
                    $matchedNode->nodeValue = $node->childNodes->item(0)->nodeValue;
                }
            } else { /* recursive merge for all child nodes */
                foreach ($node->childNodes as $childNode) {
                    if ($childNode instanceof \DOMElement) {
                        $this->_mergeNode($childNode, $path);
                    }
                }
            }
        } else {
            /* Add node as is to the document under the same parent element */
            $parentMatchedNode = $this->_getMatchedNode($parentPath);
            $newNode = $this->_dom->importNode($node, true);
            $parentMatchedNode->appendChild($newNode);
        }
    }

    /**
     * Check if the node content is text
     *
     * @param $node
     * @return bool
     */
    protected function _isTextNode($node)
    {
        return $node->childNodes->length == 1 && $node->childNodes->item(0) instanceof \DOMText;
    }

    /**
     * Merges attributes of the merge node to the base node
     *
     * @param $baseNode
     * @param $mergeNode
     * @return null
     */
    protected function _mergeAttributes($baseNode, $mergeNode)
    {
        foreach ($mergeNode->attributes as $attribute) {
            $baseNode->setAttribute($this->_getAttributeName($attribute), $attribute->value);
        }
    }

    /**
     * Identify node path based on parent path and node attributes
     *
     * @param \DOMElement $node
     * @param string $parentPath
     * @return string
     */
    protected function _getNodePathByParent(\DOMElement $node, $parentPath)
    {
        $prefix = is_null($this->_rootNamespace) ? '' : self::ROOT_NAMESPACE_PREFIX . ':';
        $path = $parentPath . '/' . $prefix . $node->tagName;
        $idAttribute = $this->_findIdAttribute($path);
        if ($idAttribute && $value = $node->getAttribute($idAttribute)) {
            $path .= "[@{$idAttribute}='{$value}']";
        }
        return $path;
    }

    /**
     * Determine whether an XPath matches registered identifiable attribute
     *
     * @param string $xPath
     * @return string|false
     */
    protected function _findIdAttribute($xPath)
    {
        $path = preg_replace('/\[@[^\]]+?\]/', '', $xPath);
        $path = preg_replace('/\/[^:]+?\:/', '/', $path);
        return isset($this->_idAttributes[$path]) ? $this->_idAttributes[$path] : false;
    }

    /**
     * Getter for node by path
     *
     * @param string $nodePath
     * @throws \Magento\Exception an exception is possible if original document contains multiple nodes for identifier
     * @return \DOMElement | null
     */
    protected function _getMatchedNode($nodePath)
    {
        $xPath  = new \DOMXPath($this->_dom);
        if ($this->_rootNamespace) {
            $xPath->registerNamespace(self::ROOT_NAMESPACE_PREFIX, $this->_rootNamespace);
        }
        $matchedNodes = $xPath->query($nodePath);
        $node = null;
        if ($matchedNodes->length > 1) {
            throw new \Magento\Exception("More than one node matching the query: {$nodePath}");
        } elseif ($matchedNodes->length == 1) {
            $node = $matchedNodes->item(0);
        }
        return $node;
    }

    /**
     * Validate dom document
     *
     * @param \DOMDocument $dom
     * @param string $schemaFileName
     * @param string $errorFormat
     * @return array of errors
     * @throws \Exception
     */
    public static function validateDomDocument(
        \DOMDocument $dom, $schemaFileName, $errorFormat = self::ERROR_FORMAT_DEFAULT
    ) {
        libxml_use_internal_errors(true);
        try {
            $result = $dom->schemaValidate($schemaFileName);
            $errors = array();
            if (!$result) {
                $validationErrors = libxml_get_errors();
                if (count($validationErrors)) {
                    foreach ($validationErrors as $error) {
                        $errors[] = self::_renderErrorMessage($error, $errorFormat);
                    }
                } else {
                    $errors[] = 'Unknown validation error';
                }
            }
        } catch (\Exception $exception) {
            libxml_use_internal_errors(false);
            throw $exception;
        }
        libxml_use_internal_errors(false);
        return $errors;
    }

    /**
     * Render error message string by replacing placeholders '%field%' with properties of \LibXMLError
     *
     * @param \LibXMLError $errorInfo
     * @param string $format
     * @return string
     * @throws \InvalidArgumentException
     */
    private static function _renderErrorMessage(\LibXMLError $errorInfo, $format)
    {
        $result = $format;
        foreach ($errorInfo as $field => $value) {
            $placeholder = '%' . $field . '%';
            $value = trim((string)$value);
            $result = str_replace($placeholder, $value, $result);
        }
        if (strpos($result, '%') !== false) {
            throw new \InvalidArgumentException("Error format '$format' contains unsupported placeholders.");
        }
        return $result;
    }

    /**
     * DOM document getter
     *
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->_dom;
    }

    /**
     * Create DOM document based on $xml parameter
     *
     * @param string $xml
     * @return \DOMDocument
     * @throws \Magento\Config\Dom\ValidationException
     */
    protected function _initDom($xml)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        if ($this->_schemaFile) {
            $errors = self::validateDomDocument($dom, $this->_schemaFile, $this->_errorFormat);
            if (count($errors)) {
                throw new \Magento\Config\Dom\ValidationException(implode("\n", $errors));
            }
        }
        return $dom;
    }

    /**
     * Validate self contents towards to specified schema
     *
     * @param string $schemaFileName absolute path to schema file
     * @param array &$errors
     * @return bool
     */
    public function validate($schemaFileName, &$errors = array())
    {
        $errors = self::validateDomDocument($this->_dom, $schemaFileName, $this->_errorFormat);
        return !count($errors);
    }

    /**
     * Set schema file
     *
     * @param string $schemaFile
     * @return \Magento\Config\Dom
     */
    public function setSchemaFile($schemaFile)
    {
        $this->_schemaFile = $schemaFile;
        return $this;
    }

    /**
     * Returns the attribute name with prefix, if there is one
     *
     * @param DOMAttr $attribute
     * @return string
     */
    private function _getAttributeName($attribute)
    {
        if (!is_null($attribute->prefix) && !empty($attribute->prefix)) {
            $attributeName = $attribute->prefix . ':' .$attribute->name;
        } else {
            $attributeName =  $attribute->name;
        }
        return $attributeName;
    }
}
