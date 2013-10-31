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

namespace Magento\Core\Model\Layout;

class Translator
{
    /**
     * Translate layout node
     *
     * @param \Magento\Simplexml\Element $node
     * @param array $args
     **/
    public function translateActionParameters(\Magento\Simplexml\Element $node, &$args)
    {
        if (false === $this->_isNodeTranslatable($node)) {
            return;
        }

        foreach ($this->_getNodeNamesToTranslate($node) as $translatableArg) {
            /*
             * .(dot) character is used as a path separator in nodes hierarchy
             * e.g. info.title means that Magento needs to translate value of <title> node
             * that is a child of <info> node
             */
            // @var $argumentHierarchy array - path to translatable item in $args array
            $argumentHierarchy = explode('.', $translatableArg);
            $argumentStack = &$args;
            $canTranslate = true;
            while (is_array($argumentStack) && count($argumentStack) > 0) {
                $argumentName = array_shift($argumentHierarchy);
                if (isset($argumentStack[$argumentName])) {
                    /*
                     * Move to the next element in arguments hierarchy
                     * in order to find target translatable argument
                     */
                    $argumentStack = &$argumentStack[$argumentName];
                } else {
                    // Target argument cannot be found
                    $canTranslate = false;
                    break;
                }
            }
            if ($canTranslate && is_string($argumentStack)) {
                // $argumentStack is now a reference to target translatable argument so it can be translated
                $argumentStack = $this->_translateValue($argumentStack);
            }
        }
    }

    /**
     * Translate argument value
     *
     * @param \Magento\Simplexml\Element $node
     * @return string
     */
    public function translateArgument(\Magento\Simplexml\Element $node)
    {
        $value = $this->_getNodeValue($node);

        if ($this->_isSelfTranslatable($node)) {
            $value = $this->_translateValue($value);
        } elseif ($this->_isNodeTranslatable($node->getParent())) {
            if (true === in_array($node->getName(), $this->_getNodeNamesToTranslate($node->getParent()))) {
                $value = $this->_translateValue($value);
            }
        }

        return $value;
    }

    /**
     * Get node names that have to be translated
     *
     * @param $node
     * @return array
     */
    protected function _getNodeNamesToTranslate(\Magento\Simplexml\Element $node)
    {
        return explode(' ', (string)$node['translate']);
    }

    /**
     * Check if node has to be translated
     *
     * @param \Magento\Simplexml\Element $node
     * @return bool
     */
    protected function _isNodeTranslatable(\Magento\Simplexml\Element $node)
    {
        return isset($node['translate']);
    }

    /**
     * Check if node has to translate own value
     *
     * @param \Magento\Simplexml\Element $node
     * @return bool
     */
    protected function _isSelfTranslatable(\Magento\Simplexml\Element $node)
    {
        return $this->_isNodeTranslatable($node) && 'true' == (string)$node['translate'];
    }

    /**
     * Get node value
     *
     * @param \Magento\Simplexml\Element $node
     * @return string
     */
    protected function _getNodeValue(\Magento\Simplexml\Element $node)
    {
        return trim((string)$node);
    }

    /**
     * Translate node value
     *
     * @param string $value
     * @return string
     */
    protected function _translateValue($value)
    {
        return __($value);
    }
}
