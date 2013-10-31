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
namespace Magento\Phrase\Renderer;

class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Translate|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_translator;

    /**
     * @var \Magento\Phrase\Renderer\Translate
     */
    protected $_renderer;

    protected function setUp()
    {
        $this->_translator = $this->getMock('Magento\Core\Model\Translate', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_renderer = $objectManagerHelper->getObject('Magento\Phrase\Renderer\Translate', array(
            'translator' => $this->_translator,
        ));
    }

    public function testTranslate()
    {
        $result = 'rendered text';

        $this->_translator->expects($this->once())->method('translate')
            ->with(array('text', 'param1', 'param2', 'param3'))
            ->will($this->returnValue($result));

        $this->assertEquals($result, $this->_renderer->render('text', array('param1', 'param2', 'param3')));
    }
}
