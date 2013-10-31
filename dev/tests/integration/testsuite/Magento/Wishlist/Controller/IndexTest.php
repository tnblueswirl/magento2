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
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Wishlist\Controller;

class IndexTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);
        $this->_customerSession = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $this->_customerSession->login('customer@example.com', 'password');
    }

    protected function tearDown()
    {
        $this->_customerSession->logout();
        $this->_customerSession = null;
        parent::tearDown();
    }

    /**
     * Verify wishlist view action
     *
     * The following is verified:
     * - \Magento\Wishlist\Model\Resource\Item\Collection
     * - \Magento\Wishlist\Block\Customer\Wishlist
     * - \Magento\Wishlist\Block\Customer\Wishlist\Items
     * - \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
     * - \Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Cart
     * - \Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Comment
     * - \Magento\Wishlist\Block\Customer\Wishlist\Button
     * - that \Magento\Wishlist\Block\Customer\Wishlist\Item\Options doesn't throw a fatal error
     *
     * @magentoDataFixture Magento/Wishlist/_files/wishlist.php
     */
    public function testItemColumnBlock()
    {
        $this->dispatch('wishlist/index/index');
        $body = $this->getResponse()->getBody();
        $this->assertSelectCount('img[src~="small_image.jpg"][alt="Simple Product"]', 1, $body);
        $this->assertSelectCount('textarea[name~="description"]', 1, $body);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_xss.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testAddActionProductNameXss()
    {
        $this->dispatch('wishlist/index/add/product/1?nocookie=1');
        $messages = $this->_customerSession->getMessages()->getItems();
        $isProductNamePresent = false;
        foreach ($messages as $message) {
            if (strpos($message->getCode(), '&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;') !== false) {
                $isProductNamePresent = true;
            }
            $this->assertNotContains('<script>alert("xss");</script>', (string)$message->getCode());
        }
        $this->assertTrue($isProductNamePresent, 'Product name was not found in session messages');
    }
}
