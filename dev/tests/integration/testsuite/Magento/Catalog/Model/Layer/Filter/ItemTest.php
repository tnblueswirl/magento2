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
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Item.
 */
namespace Magento\Catalog\Model\Layer\Filter;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Item', array(
            'data' => array(
                'filter' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Category'),
                'value'  => array('valuePart1', 'valuePart2'),
            )
        ));
    }

    public function testGetFilter()
    {
        $filter = $this->_model->getFilter();
        $this->assertInternalType('object', $filter);
        $this->assertSame($filter, $this->_model->getFilter());
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testGetFilterException()
    {
        /** @var $model \Magento\Catalog\Model\Layer\Filter\Item */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Layer\Filter\Item');
        $model->getFilter();
    }

    public function testGetUrl()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $action = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Controller\Front\Action',
            array(
                'request' => $request,
                'response' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                    ->get('Magento\TestFramework\Response'),
            )
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->getFrontController()
            ->setAction($action); // done in action's constructor
        $this->assertStringEndsWith('/?cat%5B0%5D=valuePart1&cat%5B1%5D=valuePart2', $this->_model->getUrl());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetRemoveUrl()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->create('Magento\App\RequestInterface');

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\RequestInterface')
            ->setRoutingInfo(array(
                'requested_route'      => 'x',
                'requested_controller' => 'y',
                'requested_action'     => 'z',
            ));

        $request->setParam('cat', 4);
        $this->_model->getFilter()->apply(
            $request,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
                ->createBlock('Magento\Core\Block\Text')
        );

        $this->assertStringEndsWith('/x/y/z/?cat=3', $this->_model->getRemoveUrl());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    public function testGetValueString()
    {
        $this->assertEquals('valuePart1,valuePart2', $this->_model->getValueString());
    }
}
