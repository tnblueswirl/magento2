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
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Adminhtml\Block\Newsletter;

/**
 * @magentoAppArea adminhtml
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShowQueueAdd()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Adminhtml\Block\Newsletter\Subscriber */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Newsletter\Subscriber', 'block');
        /** @var $childBlock \Magento\Core\Block\Template */
        $childBlock = $layout->addBlock('Magento\Core\Block\Template', 'grid', 'block');

        $expected = 'test_data';
        $this->assertNotEquals($expected, $block->getShowQueueAdd());
        $childBlock->setShowQueueAdd($expected);
        $this->assertEquals($expected, $block->getShowQueueAdd());
    }
}
