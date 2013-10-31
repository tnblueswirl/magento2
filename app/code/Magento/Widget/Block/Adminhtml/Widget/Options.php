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
 * @package     Magento_Widget
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WYSIWYG widget options form
 *
 * @category   Magento
 * @package    Magento_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Widget\Block\Adminhtml\Widget;

class Options extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Element type used by default if configuration is omitted
     * @var string
     */
    protected $_defaultElementType = 'text';
    
    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_widget;

    /**
     * @var \Magento\Widget\Model\Widget\Instance\OptionsFactory
     * @var \Magento\Core\Model\Option\ArrayPool
     */
    protected $_sourceModelPool;

    /**
     * @param \Magento\Core\Model\Option\ArrayPool $sourceModelPool
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Option\ArrayPool $sourceModelPool,
        \Magento\Widget\Model\Widget $widget,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_sourceModelPool = $sourceModelPool;
        $this->_widget = $widget;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare Widget Options Form and values according to specified type
     *
     * widget_type must be set in data before
     * widget_values may be set before to render element values
     */
    protected function _prepareForm()
    {
        $this->getForm()->setUseContainer(false);
        $this->addFields();
        return $this;
    }

    /**
     * Form getter/instantiation
     *
     * @return \Magento\Data\Form
     */
    public function getForm()
    {
        if ($this->_form instanceof \Magento\Data\Form) {
            return $this->_form;
        }
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $this->setForm($form);
        return $form;
    }

    /**
     * Fieldset getter/instantiation
     *
     * @return \Magento\Data\Form\Element\Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof \Magento\Data\Form\Element\Fieldset) {
            return $this->_getData('main_fieldset');
        }
        $mainFieldsetHtmlId = 'options_fieldset' . md5($this->getWidgetType());
        $this->setMainFieldsetHtmlId($mainFieldsetHtmlId);
        $fieldset = $this->getForm()->addFieldset($mainFieldsetHtmlId, array(
            'legend'    => __('Widget Options'),
            'class'     => 'fieldset-wide',
        ));
        $this->setData('main_fieldset', $fieldset);

        // add dependence javascript block
        $block = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Form\Element\Dependence');
        $this->setChild('form_after', $block);

        return $fieldset;
    }

    /**
     * Add fields to main fieldset based on specified widget type
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    public function addFields()
    {
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            throw new \Magento\Core\Exception(__('Please specify a Widget Type.'));
        }
        $config = $this->_widget->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }
        foreach ($config->getParameters() as $parameter) {
            $this->_addField($parameter);
        }

        return $this;
    }

    /**
     * Add field to Options form based on parameter configuration
     *
     * @param \Magento\Object $parameter
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    protected function _addField($parameter)
    {
        $form = $this->getForm();
        $fieldset = $this->getMainFieldset(); //$form->getElement('options_fieldset');

        // prepare element data with values (either from request of from default values)
        $fieldName = $parameter->getKey();
        $data = array(
            'name'      => $form->addSuffixToName($fieldName, 'parameters'),
            'label'     => __($parameter->getLabel()),
            'required'  => $parameter->getRequired(),
            'class'     => 'widget-option',
            'note'      => __($parameter->getDescription()),
        );

        if ($values = $this->getWidgetValues()) {
            $data['value'] = (isset($values[$fieldName]) ? $values[$fieldName] : '');
        }
        else {
            $data['value'] = $parameter->getValue();
            //prepare unique id value
            if ($fieldName == 'unique_id' && $data['value'] == '') {
                $data['value'] = md5(microtime(1));
            }
        }

        // prepare element dropdown values
        if ($values = $parameter->getValues()) {
            // dropdown options are specified in configuration
            $data['values'] = array();
            foreach ($values as $option) {
                $data['values'][] = array(
                    'label' => __($option['label']),
                    'value' => $option['value']
                );
            }
        }
        // otherwise, a source model is specified
        elseif ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = $this->_sourceModelPool->get($sourceModel)->toOptionArray();
        }

        // prepare field type or renderer
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        // hidden element
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        }
        // just an element renderer
        elseif ($fieldType && $this->_isClassName($fieldType)) {
            $fieldRenderer = $this->getLayout()->createBlock($fieldType);
            $fieldType = $this->_defaultElementType;
        }

        // instantiate field and render html
        $field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }

        // extra html preparations
        if ($helper = $parameter->getHelperBlock()) {
            $helperBlock = $this->getLayout()->createBlock($helper->getType(), '', array('data' => $helper->getData()));
            if ($helperBlock instanceof \Magento\Object) {
                $helperBlock->setConfig($helper->getData())
                    ->setFieldsetId($fieldset->getId())
                    ->prepareElementHtml($field);
            }
        }

        // dependencies from other fields
        $dependenceBlock = $this->getChildBlock('form_after');
        $dependenceBlock->addFieldMap($field->getId(), $fieldName);
        if ($parameter->getDepends()) {
            foreach ($parameter->getDepends() as $from => $row) {
                $values = isset($row['values']) ? array_values($row['values']) : (string)$row['value'];
                $dependenceBlock->addFieldDependence($fieldName, $from, $values);
            }
        }

        return $field;
    }

    /**
     * Checks whether $fieldType is a class name of custom renderer, and not just a type of input element
     *
     * @param string $fieldType
     * @return bool
     */
    protected function _isClassName($fieldType)
    {
        return preg_match('/[A-Z]/', $fieldType) > 0;
    }
}
