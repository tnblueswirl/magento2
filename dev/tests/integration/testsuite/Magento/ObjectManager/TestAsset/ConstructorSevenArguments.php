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
 * @package     Magento_ObjectManager
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\ObjectManager\TestAsset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class ConstructorSevenArguments
    extends \Magento\ObjectManager\TestAsset\ConstructorSixArguments
{
    /**
     * @var \Magento\ObjectManager\TestAsset\Basic
     */
    protected $_seven;

    /**
     * Seven arguments
     *
     * @param \Magento\ObjectManager\TestAsset\Basic $one
     * @param \Magento\ObjectManager\TestAsset\Basic $two
     * @param \Magento\ObjectManager\TestAsset\Basic $three
     * @param \Magento\ObjectManager\TestAsset\Basic $four
     * @param \Magento\ObjectManager\TestAsset\Basic $five
     * @param \Magento\ObjectManager\TestAsset\Basic $six
     * @param \Magento\ObjectManager\TestAsset\Basic $seven
     */
    public function __construct(
        \Magento\ObjectManager\TestAsset\Basic $one,
        \Magento\ObjectManager\TestAsset\Basic $two,
        \Magento\ObjectManager\TestAsset\Basic $three,
        \Magento\ObjectManager\TestAsset\Basic $four,
        \Magento\ObjectManager\TestAsset\Basic $five,
        \Magento\ObjectManager\TestAsset\Basic $six,
        \Magento\ObjectManager\TestAsset\Basic $seven
    ) {
        parent::__construct($one, $two, $three, $four, $five, $six);
        $this->_seven = $seven;
    }
}
