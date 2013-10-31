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
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Code\GeneratorTest;

use \Zend\Code\Generator\DocBlockGenerator;

class ParentClassWithoutNamespace
{
    /**
     * Public parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function publicParentMethod(
        DocBlockGenerator $docBlockGenerator,
        $param1 = '',
        $param2 = '\\',
        $param3 = '\'',
        array $array = array()
    ) {
    }

    /**
     * Protected parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _protectedParentMethod(
        DocBlockGenerator $docBlockGenerator,
        $param1 = '',
        $param2 = '\\',
        $param3 = '\'',
        array $array = array()
    ) {
    }

    /**
     * Private parent method
     *
     * @param \Zend\Code\Generator\DocBlockGenerator $docBlockGenerator
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param array $array
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function _privateParentMethod(
        DocBlockGenerator $docBlockGenerator,
        $param1 = '',
        $param2 = '\\',
        $param3 = '\'',
        array $array = array()
    ) {
    }

    public function publicParentWithoutParameters()
    {
    }

    public static function publicParentStatic()
    {
    }

    final public function publicParentFinal()
    {
    }
}
