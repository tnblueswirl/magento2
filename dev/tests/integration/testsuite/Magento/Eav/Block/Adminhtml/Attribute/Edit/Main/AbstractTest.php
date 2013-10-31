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
 * @package     Magento_Eav
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test class for \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
 */
namespace Magento\Eav\Block\Adminhtml\Attribute\Edit\Main;

class AbstractTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $entityType = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config')
            ->getEntityType('customer');
        $model = $objectManager->create('Magento\Customer\Model\Attribute');
        $model->setEntityTypeId($entityType->getId());
        $objectManager->get('Magento\Core\Model\Registry')->register('entity_attribute', $model);

        $block = $this->getMockForAbstractClass(
            'Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain',
            array(
                $objectManager->get('Magento\Core\Model\Registry'),
                $objectManager->get('Magento\Data\Form\Factory'),
                $objectManager->get('Magento\Core\Helper\Data'),
                $objectManager->get('Magento\Backend\Block\Template\Context'),
                $objectManager->get('Magento\Eav\Helper\Data'),
                $objectManager->get('Magento\Core\Model\LocaleInterface'),
                $objectManager->get('Magento\Backend\Model\Config\Source\YesnoFactory'),
                $objectManager->get('Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory'),
                $objectManager->get('Magento\Eav\Model\Entity\Attribute\Config')
            )
        )
        ->setLayout($objectManager->create('Magento\Core\Model\Layout'));

        $method = new \ReflectionMethod(
            'Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain', '_prepareForm');
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
