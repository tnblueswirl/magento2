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
 * @package     Magento_Paypal
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for \Magento\Paypal\Block\Express\Review
 */
namespace Magento\Paypal\Block\Express;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderAddress()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Paypal\Block\Express\Review');
        $addressData = include(__DIR__ . '/../../../Sales/_files/address_data.php');
        $address = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote\Address', array('data' => $addressData));
        $address->setAddressType('billing');
        $this->assertContains('Los Angeles', $block->renderAddress($address));
    }
}
