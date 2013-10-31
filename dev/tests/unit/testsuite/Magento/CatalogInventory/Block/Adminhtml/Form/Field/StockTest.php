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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var \Magento\Core\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var \Magento\Data\Form\Element\Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryElementMock;

    /**
     * @var \Magento\Data\Form\Element\CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactoryMock;
    
    /**
     * @var \Magento\Data\Form\Element\Text|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_qtyMock;

    /**
     * @var \Magento\Data\Form\Element\TextFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryTextMock;
    
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_block;

    protected function setUp()
    {
        $this->_coreHelperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);        
        $this->_factoryElementMock = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $this->_collectionFactoryMock = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array(),
            array(), '', false);
        $this->_qtyMock = $this->getMock('Magento\Data\Form\Element\Text', array('setForm', 'setValue', 'setName'),
            array(), '', false);
        $this->_factoryTextMock = $this->getMock('Magento\Data\Form\Element\TextFactory', array('create'));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock',
            array(
                'coreData' => $this->_coreHelperMock,
                'factoryElement' => $this->_factoryElementMock,
                'factoryCollection' => $this->_collectionFactoryMock,
                'factoryText' => $this->_factoryTextMock,
                'attributes' => array(
                    'qty' => $this->_qtyMock,
                    'name' => self::ATTRIBUTE_NAME,
                ),
            )
        );
    }
    
    public function testSetForm()
    {
        $this->_qtyMock->expects($this->once())->method('setForm')
            ->with($this->isInstanceOf('Magento\Data\Form\Element\AbstractElement'));

        $this->_block->setForm(new \Magento\Data\Form\Element\Text(
            $this->_coreHelperMock, 
            $this->_factoryElementMock, 
            $this->_collectionFactoryMock
        ));
    }

    public function testSetValue()
    {
        $value = array('qty' => 1, 'is_in_stock' => 0);
        $this->_qtyMock->expects($this->once())->method('setValue')->with($this->equalTo(1));

        $this->_block->setValue($value);
    }

    public function testSetName()
    {
        $this->_qtyMock->expects($this->once())->method('setName')->with(self::ATTRIBUTE_NAME . '[qty]');

        $this->_block->setName(self::ATTRIBUTE_NAME);
    }
}
