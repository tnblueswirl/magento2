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

namespace Magento\Adminhtml\Block\Urlrewrite\Edit;

/**
 * Test for \Magento\Adminhtml\Block\Urlrewrite\Edit\FormTest
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get form instance
     *
     * @param array $args
     * @return \Magento\Data\Form
     */
    protected function _getFormInstance($args = array())
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Adminhtml\Block\Urlrewrite\Edit\Form */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Urlrewrite\Edit\Form', 'block', array('data' => $args));
        $block->setTemplate(null);
        $block->toHtml();
        return $block->getForm();
    }

    /**
     * Test that form was prepared correctly
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        // Test form was configured correctly
        $form = $this->_getFormInstance(array('url_rewrite' => new \Magento\Object(array('id' => 3))));
        $this->assertInstanceOf('Magento\Data\Form', $form);
        $this->assertNotEmpty($form->getAction());
        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $this->assertTrue($form->getUseContainer());
        $this->assertContains('/id/3', $form->getAction());

        // Check all expected form elements are present
        $expectedElements = array(
            'is_system',
            'id_path',
            'request_path',
            'target_path',
            'options',
            'description',
            'store_id'
        );
        foreach ($expectedElements as $expectedElement) {
            $this->assertNotNull($form->getElement($expectedElement));
        }
    }

    /**
     * Check session data restoring
     * @magentoAppIsolation enabled
     */
    public function testSessionRestore()
    {
        // Set urlrewrite data to session
        $sessionValues = array(
            'store_id'     => 1,
            'id_path'      => 'id_path',
            'request_path' => 'request_path',
            'target_path'  => 'target_path',
            'options'      => 'options',
            'description'  => 'description'
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Adminhtml\Model\Session')->setUrlrewriteData($sessionValues);
        // Re-init form to use newly set session data
        $form = $this->_getFormInstance(array('url_rewrite' => new \Magento\Object()));

        // Check that all fields values are restored from session
        foreach ($sessionValues as $field => $value) {
            $this->assertEquals($value, $form->getElement($field)->getValue());
        }
    }

    /**
     * Test store element is hidden when only one store available
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testStoreElementSingleStore()
    {
        $form = $this->_getFormInstance(array('url_rewrite' => new \Magento\Object(array('id' => 3))));
        /** @var $storeElement \Magento\Data\Form\Element\AbstractElement */
        $storeElement = $form->getElement('store_id');
        $this->assertInstanceOf('Magento\Data\Form\Element\Hidden', $storeElement);

        // Check that store value set correctly
        $defaultStore = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore(true)->getId();
        $this->assertEquals($defaultStore, $storeElement->getValue());
    }

    /**
     * Test store selection is available and correctly configured
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testStoreElementMultiStores()
    {
        $form = $this->_getFormInstance(array('url_rewrite' => new \Magento\Object(array('id' => 3))));
        /** @var $storeElement \Magento\Data\Form\Element\AbstractElement */
        $storeElement = $form->getElement('store_id');

        // Check store selection elements has correct type
        $this->assertInstanceOf('Magento\Data\Form\Element\Select', $storeElement);

        // Check store selection elements has correct renderer
        $this->assertInstanceOf('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element',
            $storeElement->getRenderer());

        // Check store elements has expected values
        $storesList = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\System\Store')
            ->getStoreValuesForForm();
        $this->assertInternalType('array', $storeElement->getValues());
        $this->assertNotEmpty($storeElement->getValues());
        $this->assertEquals($storesList, $storeElement->getValues());
    }

    /**
     * Test fields disabled status
     * @dataProvider fieldsStateDataProvider
     * @magentoAppIsolation enabled
     */
    public function testDisabledFields($urlRewrite, $fields)
    {
        $form = $this->_getFormInstance(array('url_rewrite' => $urlRewrite));
        foreach ($fields as $fieldKey => $expected) {
            $this->assertEquals($expected, $form->getElement($fieldKey)->getDisabled());
        }
    }

    /**
     * Data provider for checking fields state
     */
    public function fieldsStateDataProvider()
    {
        return array(
            array(
                new \Magento\Object(),
                array(
                    'is_system'    => true,
                    'id_path'      => false,
                    'request_path' => false,
                    'target_path'  => false,
                    'options'      => false,
                    'description'  => false
                )
            ),
            array(
                new \Magento\Object(array('id' => 3)),
                array(
                    'is_system'    => true,
                    'id_path'      => false,
                    'request_path' => false,
                    'target_path'  => false,
                    'options'      => false,
                    'description'  => false
                )
            )
        );
    }
}
