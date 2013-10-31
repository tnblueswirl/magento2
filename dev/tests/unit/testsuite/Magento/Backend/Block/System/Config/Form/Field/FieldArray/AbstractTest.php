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
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Backend\Block\System\Config\Form\Field\FieldArray;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArrayRows()
    {
        /** @var $block \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray */
        $block = $this->getMockForAbstractClass(
            'Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray',
            array(),
            '',
            false,
            true,
            true,
            array('escapeHtml')
        );
        $block->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $coreHelper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $element = new \Magento\Data\Form\Element\Multiselect($coreHelper, $factory, $collectionFactory);
        $element->setValue(array(
            array(
                'test' => 'test',
                'data1' => 'data1',
            )
        ));
        $block->setElement($element);
        $this->assertEquals(
            array(
                new \Magento\Object(array(
                    'test' => 'test',
                    'data1' => 'data1',
                    '_id' => 0,
                    'column_values' => array(
                        '0_test' => 'test',
                        '0_data1' => 'data1',
                    ),
                ))
            ),
            $block->getArrayRows()
        );
    }
}
