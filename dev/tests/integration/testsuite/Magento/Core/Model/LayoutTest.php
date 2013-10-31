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
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layout integration tests
 *
 * Note that some methods are not covered here, see the \Magento\Core\Model\LayoutDirectivesTest
 *
 * @see \Magento\Core\Model\LayoutDirectivesTest
 */
namespace Magento\Core\Model;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
    }

    /**
     * @param array $inputArguments
     * @param string $expectedArea
     * @dataProvider constructorDataProvider
     */
    public function testConstructor(array $inputArguments, $expectedArea)
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout', $inputArguments);
        $this->assertEquals($expectedArea, $layout->getArea());
    }

    public function constructorDataProvider()
    {
        return array(
            'default area'  => array(array(), \Magento\View\DesignInterface::DEFAULT_AREA),
            'frontend area' => array(array('area' => 'frontend'), 'frontend'),
            'backend area'  => array(array('area' => 'adminhtml'), 'adminhtml'),
        );
    }

    public function testConstructorStructure()
    {
        $structure = new \Magento\Data\Structure;
        $structure->createElement('test.container', array());
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout', array('structure' => $structure));
        $this->assertTrue($layout->hasElement('test.container'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDestructor()
    {
        $this->_layout->addBlock('Magento\Core\Block\Text', 'test');
        $this->assertNotEmpty($this->_layout->getAllBlocks());
        $this->_layout->__destruct();
        $this->assertEmpty($this->_layout->getAllBlocks());
    }

    public function testGetUpdate()
    {
        $this->assertInstanceOf('Magento\View\Layout\ProcessorInterface', $this->_layout->getUpdate());
    }

    public function testGenerateXml()
    {
        $layoutUtility = new \Magento\Core\Utility\Layout($this);
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $this->getMock('Magento\Core\Model\Layout', array('getUpdate'),
            $layoutUtility->getLayoutDependencies());
        $merge = $this->getMock('StdClass', array('asSimplexml'));
        $merge->expects($this->once())->method('asSimplexml')->will($this->returnValue(simplexml_load_string(
            '<layout><container name="container1"></container></layout>',
            'Magento\View\Layout\Element'
        )));
        $layout->expects($this->once())->method('getUpdate')->will($this->returnValue($merge));
        $this->assertEmpty($layout->getXpath('/layout/container[@name="container1"]'));
        $layout->generateXml();
        $this->assertNotEmpty($layout->getXpath('/layout/container[@name="container1"]'));
    }

    /**
     * A smoke test for generating elements
     *
     * See sophisticated tests at \Magento\Core\Model\LayoutDirectivesTest
     * @see \Magento\Core\Model\LayoutDirectivesTest
     * @magentoAppIsolation enabled
     */
    public function testGenerateGetAllBlocks()
    {
        $this->_layout->setXml(simplexml_load_string(
            '<layout>
                <block class="Magento\Core\Block\Text" name="block1">
                    <block class="Magento\Core\Block\Text"/>
                </block>
                <block class="Magento\Core\Block\Text" template="test"/>
                <block class="Magento\Core\Block\Text"/>
            </layout>',
            'Magento\View\Layout\Element'
        ));
        $this->assertEquals(array(), $this->_layout->getAllBlocks());
        $this->_layout->generateElements();
        $expected = array('block1', 'block1_schedule_block', 'schedule_block', 'schedule_block_1');
        $this->assertSame($expected, array_keys($this->_layout->getAllBlocks()));
        $child = $this->_layout->getBlock('block1_schedule_block');
        $this->assertSame($this->_layout->getBlock('block1'), $child->getParentBlock());
        $this->assertEquals('test', $this->_layout->getBlock('schedule_block')->getData('template'));
        $this->assertFalse($this->_layout->getBlock('nonexisting'));
    }

    public function testGetElementProperty()
    {
        $name = 'test';
        $this->_layout->addContainer($name, 'Test', array('option1' => 1, 'option2' => 2));
        $this->assertEquals('Test', $this->_layout->getElementProperty(
            $name, \Magento\Core\Model\Layout::CONTAINER_OPT_LABEL
        ));
        $this->assertEquals(\Magento\Core\Model\Layout::TYPE_CONTAINER,
            $this->_layout->getElementProperty($name, 'type'));
        $this->assertSame(2, $this->_layout->getElementProperty($name, 'option2'));

        $this->_layout->addBlock('Magento\Core\Block\Text', 'text', $name);
        $this->assertEquals(\Magento\Core\Model\Layout::TYPE_BLOCK, $this->_layout->getElementProperty('text', 'type'));
        $this->assertSame(array('text' => 'text'), $this->_layout->getElementProperty(
            $name, \Magento\Data\Structure::CHILDREN
        ));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testIsBlock()
    {
        $this->assertFalse($this->_layout->isBlock('container'));
        $this->assertFalse($this->_layout->isBlock('block'));
        $this->_layout->addContainer('container', 'Container');
        $this->_layout->addBlock('Magento\Core\Block\Text', 'block');
        $this->assertFalse($this->_layout->isBlock('container'));
        $this->assertTrue($this->_layout->isBlock('block'));
    }

    public function testSetUnsetBlock()
    {
        $expectedBlockName = 'block_' . __METHOD__;
        $expectedBlock = $this->_layout->createBlock('Magento\Core\Block\Text');

        $this->_layout->setBlock($expectedBlockName, $expectedBlock);
        $this->assertSame($expectedBlock, $this->_layout->getBlock($expectedBlockName));

        $this->_layout->unsetElement($expectedBlockName);
        $this->assertFalse($this->_layout->getBlock($expectedBlockName));
        $this->assertFalse($this->_layout->hasElement($expectedBlockName));
    }

    /**
     * @dataProvider createBlockDataProvider
     */
    public function testCreateBlock($blockType, $blockName, array $blockData, $expectedName)
    {
        $expectedData = $blockData + array('type' => $blockType);

        $block = $this->_layout->createBlock($blockType, $blockName, array('data' => $blockData));

        $this->assertEquals($this->_layout, $block->getLayout());
        $this->assertRegExp($expectedName, $block->getNameInLayout());
        $this->assertEquals($expectedData, $block->getData());
    }

    public function createBlockDataProvider()
    {
        return array(
            'named block' => array(
                'Magento\Core\Block\Template',
                'some_block_name_full_class',
                array('type' => 'Magento\Core\Block\Template', 'is_anonymous' => false),
                '/^some_block_name_full_class$/'
            ),
            'no name block' => array(
                'Magento\Core\Block\Text\ListText',
                '',
                array(
                    'type' => 'Magento\Core\Block\Text\ListText',
                    'key1' => 'value1',
                ),
                '/text\\\\list/'
            ),
        );
    }

    /**
     * @dataProvider blockNotExistsDataProvider
     * @expectedException \Magento\Core\Exception
     */
    public function testCreateBlockNotExists($name)
    {
        $this->_layout->createBlock($name);
    }

    public function blockNotExistsDataProvider()
    {
        return array(
            array(''),
            array('block_not_exists'),
        );
    }

    public function testAddBlock()
    {
        $this->assertInstanceOf('Magento\Core\Block\Text', $this->_layout->addBlock('Magento\Core\Block\Text',
            'block1'));
        $block2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Core\Block\Text');
        $block2->setNameInLayout('block2');
        $this->_layout->addBlock($block2, '', 'block1');

        $this->assertTrue($this->_layout->hasElement('block1'));
        $this->assertTrue($this->_layout->hasElement('block2'));
        $this->assertEquals('block1', $this->_layout->getParentName('block2'));
    }

    /**
     * @magentoAppIsolation enabled
     * @dataProvider addContainerDataProvider()
     */
    public function testAddContainer($htmlTag)
    {
        $this->assertFalse($this->_layout->hasElement('container'));
        $this->_layout->addContainer('container', 'Container', array('htmlTag' => $htmlTag));
        $this->assertTrue($this->_layout->hasElement('container'));
        $this->assertTrue($this->_layout->isContainer('container'));
        $this->assertEquals($htmlTag, $this->_layout->getElementProperty('container', 'htmlTag'));

        $this->_layout->addContainer('container1', 'Container 1', array(), 'container', 'c1');
        $this->assertEquals('container1', $this->_layout->getChildName('container', 'c1'));
    }

    public function addContainerDataProvider()
    {
        return array(
            array('dd'),
            array('div'),
            array('dl'),
            array('fieldset'),
            array('header'),
            array('hgroup'),
            array('ol'),
            array('p'),
            array('section'),
            array('table'),
            array('tfoot'),
            array('ul')
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testAddContainerInvalidHtmlTag()
    {
        $msg = 'Html tag "span" is forbidden for usage in containers. ' .
               'Consider to use one of the allowed: dd, div, dl, fieldset, header, ' .
               'footer, hgroup, ol, p, section, table, tfoot, ul.';
        $this->setExpectedException('Magento\Exception', $msg);
        $this->_layout->addContainer('container', 'Container', array('htmlTag' => 'span'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetChildBlock()
    {
        $this->_layout->addContainer('parent', 'Parent');
        $block = $this->_layout->addBlock('Magento\Core\Block\Text', 'block', 'parent', 'block_alias');
        $this->_layout->addContainer('container', 'Container', array(), 'parent', 'container_alias');
        $this->assertSame($block, $this->_layout->getChildBlock('parent', 'block_alias'));
        $this->assertFalse($this->_layout->getChildBlock('parent', 'container_alias'));
    }

    /**
     * @return \Magento\Core\Model\Layout
     */
    public function testSetChild()
    {
        $this->_layout->addContainer('one', 'One');
        $this->_layout->addContainer('two', 'Two');
        $this->_layout->addContainer('three', 'Three');
        $this->assertSame($this->_layout, $this->_layout->setChild('one', 'two', ''));
        $this->_layout->setChild('one', 'three', '');
        $this->assertSame(array('two', 'three'), $this->_layout->getChildNames('one'));
        return $this->_layout;
    }

    /**
     * @param \Magento\Core\Model\Layout $layout
     * @depends testSetChild
     */
    public function testReorderChild(\Magento\Core\Model\Layout $layout)
    {
        $layout->addContainer('four', 'Four', array(), 'one');

        // offset +1
        $layout->reorderChild('one', 'four', 1);
        $this->assertSame(array('two', 'four', 'three'), $layout->getChildNames('one'));

        // offset -2
        $layout->reorderChild('one', 'three', 2, false);
        $this->assertSame(array('two', 'three', 'four'), $layout->getChildNames('one'));

        // after sibling
        $layout->reorderChild('one', 'two', 'three');
        $this->assertSame(array('three', 'two', 'four'), $layout->getChildNames('one'));

        // after everyone
        $layout->reorderChild('one', 'three', '-');
        $this->assertSame(array('two', 'four', 'three'), $layout->getChildNames('one'));

        // before sibling
        $layout->reorderChild('one', 'four', 'two', false);
        $this->assertSame(array('four', 'two', 'three'), $layout->getChildNames('one'));

        // before everyone
        $layout->reorderChild('one', 'two', '-', false);
        $this->assertSame(array('two', 'four', 'three'), $layout->getChildNames('one'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetChildBlocks()
    {
        $this->_layout->addContainer('parent', 'Parent');
        $block1 = $this->_layout->addBlock('Magento\Core\Block\Text', 'block1', 'parent');
        $this->_layout->addContainer('container', 'Container', array(), 'parent');
        $block2 = $this->_layout->addBlock('Magento\Core\Block\Template', 'block2', 'parent');
        $this->assertSame(array('block1' => $block1, 'block2' => $block2), $this->_layout->getChildBlocks('parent'));
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testAddBlockInvalidType()
    {
        $this->_layout->addBlock('invalid_name', 'child');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testIsContainer()
    {
        $block = 'block';
        $container = 'container';
        $this->_layout->addBlock('Magento\Core\Block\Text', $block);
        $this->_layout->addContainer($container, 'Container');
        $this->assertFalse($this->_layout->isContainer($block));
        $this->assertTrue($this->_layout->isContainer($container));
        $this->assertFalse($this->_layout->isContainer('invalid_name'));
    }

    public function testIsManipulationAllowed()
    {
        $this->_layout->addBlock('Magento\Core\Block\Text', 'block1');
        $this->_layout->addBlock('Magento\Core\Block\Text', 'block2', 'block1');
        $this->assertFalse($this->_layout->isManipulationAllowed('block1'));
        $this->assertFalse($this->_layout->isManipulationAllowed('block2'));

        $this->_layout->addContainer('container1', 'Container 1');
        $this->_layout->addBlock('Magento\Core\Block\Text', 'block3', 'container1');
        $this->_layout->addContainer('container2', 'Container 2', array(), 'container1');
        $this->assertFalse($this->_layout->isManipulationAllowed('container1'));
        $this->assertTrue($this->_layout->isManipulationAllowed('block3'));
        $this->assertTrue($this->_layout->isManipulationAllowed('container2'));
    }

    public function testRenameElement()
    {
        $blockName = 'block';
        $expBlockName = 'block_renamed';
        $containerName = 'container';
        $expContainerName = 'container_renamed';
        $block = $this->_layout->createBlock('Magento\Core\Block\Text', $blockName);
        $this->_layout->addContainer($containerName, 'Container');

        $this->assertEquals($block, $this->_layout->getBlock($blockName));
        $this->_layout->renameElement($blockName, $expBlockName);
        $this->assertEquals($block, $this->_layout->getBlock($expBlockName));

        $this->_layout->hasElement($containerName);
        $this->_layout->renameElement($containerName, $expContainerName);
        $this->_layout->hasElement($expContainerName);
    }

    public function testGetBlock()
    {
        $this->assertFalse($this->_layout->getBlock('test'));
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Core\Block\Text');
        $this->_layout->setBlock('test', $block);
        $this->assertSame($block, $this->_layout->getBlock('test'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetParentName()
    {
        $this->_layout->addContainer('one', 'One');
        $this->_layout->addContainer('two', 'Two', array(), 'one');
        $this->assertFalse($this->_layout->getParentName('one'));
        $this->assertEquals('one', $this->_layout->getParentName('two'));
    }

    public function testGetElementAlias()
    {
        $this->_layout->addContainer('one', 'One');
        $this->_layout->addContainer('two', 'One', array(), 'one', '1');
        $this->assertFalse($this->_layout->getElementAlias('one'));
        $this->assertEquals('1', $this->_layout->getElementAlias('two'));
    }

    /**
     * @covers \Magento\Core\Model\Layout::addOutputElement
     * @covers \Magento\Core\Model\Layout::getOutput
     * @covers \Magento\Core\Model\Layout::removeOutputElement
     */
    public function testGetOutput()
    {
        $blockName = 'block_' . __METHOD__;
        $expectedText = "some_text_for_$blockName";

        $block = $this->_layout->addBlock('Magento\Core\Block\Text', $blockName);
        $block->setText($expectedText);

        $this->_layout->addOutputElement($blockName);
        // add the same element twice should not produce output duplicate
        $this->_layout->addOutputElement($blockName);
        $this->assertEquals($expectedText, $this->_layout->getOutput());

        $this->_layout->removeOutputElement($blockName);
        $this->assertEmpty($this->_layout->getOutput());
    }

    public function testGetMessagesBlock()
    {
        $this->assertInstanceOf('Magento\Core\Block\Messages', $this->_layout->getMessagesBlock());
    }

    public function testGetBlockSingleton()
    {
        $block = $this->_layout->getBlockSingleton('Magento\Core\Block\Text');
        $this->assertInstanceOf('Magento\Core\Block\Text', $block);
        $this->assertSame($block, $this->_layout->getBlockSingleton('Magento\Core\Block\Text'));
    }

    public function testUpdateContainerAttributes()
    {
        $this->_layout->setXml(simplexml_load_file(__DIR__ . '/_files/layout/container_attributes.xml',
            'Magento\View\Layout\Element'));
        $this->_layout->generateElements();
        $result = $this->_layout->renderElement('container1', false);
        $this->assertEquals('<div id="container1-2" class="class12">Test11Test12</div>', $result);
        $result = $this->_layout->renderElement('container2', false);
        $this->assertEquals('<div id="container2-2" class="class22">Test21Test22</div>', $result);
    }
}
