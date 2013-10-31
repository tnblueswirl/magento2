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

namespace Magento\Adminhtml\Block\Widget;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetButtonsHtml()
    {
        $titles = array(1 => 'Title 1', 'Title 2', 'Title 3');
        $block = $this->_buildBlock($titles);
        $html = $block->getButtonsHtml();

        $this->assertContains('<button', $html);
        foreach ($titles as $title) {
            $this->assertContains($title, $html);
        }
    }

    public function testUpdateButton()
    {
        $originalTitles = array(1 => 'Title 1', 'Title 2', 'Title 3');
        $newTitles = array(1 => 'Button A', 'Button B', 'Button C');

        $block = $this->_buildBlock($originalTitles);
        $html = $block->getButtonsHtml();
        foreach ($newTitles as $newTitle) {
            $this->assertNotContains($newTitle, $html);
        }

        $block = $this->_buildBlock($originalTitles); // Layout caches html, thus recreate block for further testing
        foreach ($newTitles as $id => $newTitle) {
            $block->updateButton($id, 'title', $newTitle);
        }
        $html = $block->getButtonsHtml();
        foreach ($newTitles as $newTitle) {
            $this->assertContains($newTitle, $html);
        }
    }

    /**
     * Composes a container with several buttons in it
     *
     * @param array $titles
     * @return \Magento\Adminhtml\Block\Widget\Container
     */
    protected function _buildBlock($titles)
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        /** @var $block \Magento\Adminhtml\Block\Widget\Container */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Widget\Container', 'block');
        foreach ($titles as $id => $title) {
            $block->addButton($id, array('title' => $title));
        }
        return $block;
    }
}
