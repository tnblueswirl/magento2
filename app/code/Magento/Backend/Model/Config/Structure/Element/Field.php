<?php
/**
 * Represents a Field Element on the UI that can be configured via xml.
 *
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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Backend\Model\Config\Structure\Element;

class Field
    extends \Magento\Backend\Model\Config\Structure\AbstractElement
{

    /**
     * Default 'value' field for service option
     */
    const DEFAULT_VALUE_FIELD = 'id';

    /**
     * Default 'label' field for service option
     */
    const DEFAULT_LABEL_FIELD = 'name';

    /**
     * Default value for useEmptyValueOption for service option
     */
    const DEFAULT_INCLUDE_EMPTY_VALUE_OPTION = false;

    /**
     * Backend model factory
     *
     * @var \Magento\Backend\Model\Config\BackendFactory
     */
    protected $_backendFactory;

    /**
     * Source model factory
     *
     * @var \Magento\Backend\Model\Config\SourceFactory
     */
    protected $_sourceFactory;

    /**
     * Comment model factory
     *
     * @var \Magento\Backend\Model\Config\CommentFactory
     */
    protected $_commentFactory;

    /**
     *
     * @var \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper
     */
    protected $_dependencyMapper;

    /**
     * Block factory
     *
     * @var \Magento\Core\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * dataservice graph
     *
     * @var \Magento\Core\Model\DataService\Graph
     */
     protected $_dataServiceGraph;

    /**
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Backend\Model\Config\BackendFactory $backendFactory
     * @param \Magento\Backend\Model\Config\SourceFactory $sourceFactory
     * @param \Magento\Backend\Model\Config\CommentFactory $commentFactory
     * @param \Magento\Core\Model\BlockFactory $blockFactory
     * @param \Magento\Core\Model\DataService\Graph $dataServiceGraph,
     * @param \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper $dependencyMapper
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Backend\Model\Config\BackendFactory $backendFactory,
        \Magento\Backend\Model\Config\SourceFactory $sourceFactory,
        \Magento\Backend\Model\Config\CommentFactory $commentFactory,
        \Magento\Core\Model\BlockFactory $blockFactory,
        \Magento\Core\Model\DataService\Graph $dataServiceGraph,
        \Magento\Backend\Model\Config\Structure\Element\Dependency\Mapper $dependencyMapper
    ) {
        parent::__construct($application);
        $this->_backendFactory = $backendFactory;
        $this->_sourceFactory = $sourceFactory;
        $this->_commentFactory = $commentFactory;
        $this->_blockFactory = $blockFactory;
        $this->_dataServiceGraph = $dataServiceGraph;
        $this->_dependencyMapper = $dependencyMapper;
    }

    /**
     * Retrieve field label
     *
     * @param string $labelPrefix
     * @return string
     */
    public function getLabel($labelPrefix = '')
    {
        $label = '';
        if ($labelPrefix) {
            $label .= $this->_translateLabel($labelPrefix) . ' ';
        }
        $label .= parent::getLabel();
        return $label;
    }

    /**
     * Retrieve field hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->_getTranslatedAttribute('hint');
    }

    /**
     * Retrieve comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getComment($currentValue = '')
    {
        $comment = '';
        if (isset($this->_data['comment'])) {
            if (is_array($this->_data['comment'])) {
                if (isset($this->_data['comment']['model'])) {
                    $model = $this->_commentFactory->create($this->_data['comment']['model']);
                    $comment = $model->getCommentText($currentValue);
                }
            } else {
                $comment = parent::getComment();
            }
        }
        return $comment;
    }

    /**
     * Retrieve tooltip text
     *
     * @return string
     */
    public function getTooltip()
    {
        if (isset($this->_data['tooltip'])) {
            return $this->_getTranslatedAttribute('tooltip');
        } elseif (isset($this->_data['tooltip_block'])) {
            return $this->_blockFactory->createBlock($this->_data['tooltip_block'])->toHtml();
        }
        return '';
    }

    /**
     * Retrieve field type
     *
     * @return string
     */
    public function getType()
    {
        return isset($this->_data['type']) ? $this->_data['type'] : 'text';
    }

    /**
     * Get required elements paths for the field
     *
     * @param string $fieldPrefix
     * @param string $elementType
     * @return array
     */
    protected function _getRequiredElements($fieldPrefix = '', $elementType = 'group')
    {
        $elements = array();
        if (isset($this->_data['requires'][$elementType])) {
            if (isset($this->_data['requires'][$elementType]['id'])) {
                $elements[] = $this->_getPath($this->_data['requires'][$elementType]['id'], $fieldPrefix);
            } else {
                foreach ($this->_data['requires'][$elementType] as $element) {
                    $elements[] = $this->_getPath($element['id'], $fieldPrefix);
                }
            }
        }
        return $elements;
    }

    /**
     * Get required groups paths for the field
     *
     * @param string $fieldPrefix
     * @return array
     */
    public function getRequiredGroups($fieldPrefix = '')
    {
        return $this->_getRequiredElements($fieldPrefix, 'group');
    }


    /**
     * Get required fields paths for the field
     *
     * @param string $fieldPrefix
     * @return array
     */
    public function getRequiredFields($fieldPrefix = '')
    {
        return $this->_getRequiredElements($fieldPrefix, 'field');
    }

    /**
     * Retrieve frontend css class
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return isset($this->_data['frontend_class']) ? $this->_data['frontend_class'] : '';
    }

    /**
     * Check whether field has backend model
     *
     * @return bool
     */
    public function hasBackendModel()
    {
        return array_key_exists('backend_model', $this->_data) && $this->_data['backend_model'];
    }

    /**
     * Retrieve backend model
     *
     * @return \Magento\Core\Model\Config\Value
     */
    public function getBackendModel()
    {
        return $this->_backendFactory->create($this->_data['backend_model']);
    }

    /**
     * Retrieve field section id
     *
     * @return string
     */
    public function getSectionId()
    {
        $parts = explode('/', $this->getConfigPath() ?: $this->getPath());
        return current($parts);
    }

    /**
     * Retrieve field group path
     *
     * @return string
     */
    public function getGroupPath()
    {
        return dirname($this->getConfigPath() ?: $this->getPath());
    }

    /**
     * Retrieve config path
     *
     * @return null|string
     */
    public function getConfigPath()
    {
        return isset($this->_data['config_path']) ? $this->_data['config_path'] : null;
    }

    /**
     * Check whether field should be shown in default scope
     *
     * @return bool
     */
    public function showInDefault()
    {
        return isset($this->_data['showInDefault']) && (int)$this->_data['showInDefault'];
    }

    /**
     * Check whether field should be shown in website scope
     *
     * @return bool
     */
    public function showInWebsite()
    {
        return isset($this->_data['showInWebsite']) && (int)$this->_data['showInWebsite'];
    }

    /**
     * Check whether field should be shown in store scope
     *
     * @return bool
     */
    public function showInStore()
    {
        return isset($this->_data['showInStore']) && (int)$this->_data['showInStore'];
    }

    /**
     * Populate form element with field data
     *
     * @param \Magento\Data\Form\Element\AbstractElement $formField
     */
    public function populateInput($formField)
    {
        $originalData = array();
        foreach ($this->_data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $formField->setOriginalData($originalData);
    }

    /**
     * Check whether field has validation class
     *
     * @return bool
     */
    public function hasValidation()
    {
        return isset($this->_data['validate']);
    }

    /**
     * Retrieve field validation class
     *
     * @return string
     */
    public function getValidation()
    {
        return isset($this->_data['validate']) ? $this->_data['validate'] : null;
    }

    /**
     * Check whether field can be empty
     *
     * @return bool
     */
    public function canBeEmpty()
    {
        return isset($this->_data['can_be_empty']);
    }

    /**
     * Check whether field has source model
     *
     * @return bool
     */
    public function hasSourceModel()
    {
        return isset($this->_data['source_model']);
    }

    /**
     * Check whether field has options or source model
     *
     * @return bool
     */
    public function hasOptions()
    {
        return isset($this->_data['source_model']) || isset($this->_data['options'])
            || isset($this->_data['source_service']);
    }

    /**
     * Retrieve static options, source service options or source model option list
     *
     * @return array
     */
    public function getOptions()
    {
        if (isset($this->_data['source_model'])) {
            $sourceModel = $this->_data['source_model'];
            $optionArray = $this->_getOptionsFromSourceModel($sourceModel);
            return $optionArray;
        } else if (isset($this->_data['source_service'])) {
            $sourceService = $this->_data['source_service'];
            $options = $this->_getOptionsFromService($sourceService);
            return $options;
        } else if (isset($this->_data['options']) && isset($this->_data['options']['option'])) {
            $options = $this->_data['options']['option'];
            $options = $this->_getStaticOptions($options);
            return $options;
        }
        return array();
    }

    /**
     * Get Static Options list
     *
     * @param array $options
     * @return array
     */
    protected  function _getStaticOptions(array $options)
    {
        foreach (array_keys($options) as $key) {
            $options[$key]['label'] = $this->_translateLabel($options[$key]['label']);
            $options[$key]['value'] = $this->_fillInConstantPlaceholders($options[$key]['value']);
        }
        return $options;
    }

    /**
     * Retrieve the options list from the specified service call.
     *
     * @param array $sourceService
     * @return array
     */
    protected function _getOptionsFromService($sourceService)
    {
        $valueField = self::DEFAULT_VALUE_FIELD;
        $labelField = self::DEFAULT_LABEL_FIELD;
        $inclEmptyValOption = self::DEFAULT_INCLUDE_EMPTY_VALUE_OPTION;
        $serviceCall = $sourceService['service_call'];
        if (isset($sourceService['idField'])) {
            $valueField = $sourceService['idField'];
        }
        if (isset($sourceService['labelField'])) {
            $labelField = $sourceService['labelField'];
        }
        if (isset($sourceService['includeEmptyValueOption'])) {
            $inclEmptyValOption = $sourceService['includeEmptyValueOption'];
        }
        $dataCollection = $this->_dataServiceGraph->get($serviceCall);
        $options = array();
        if ($inclEmptyValOption) {
            $options[] = array('value' => '', 'label' => '-- Please Select --');
        }
        foreach ($dataCollection as $dataItem) {
            $options[] = array(
                'value' => $dataItem[$valueField],
                'label' => $this->_translateLabel($dataItem[$labelField])
            );
        }
        return $options;
    }

    /**
     * Translate a label
     *
     * @param string $label an option label that should be translated
     * @return string the translated version of the input label
     */
    private function _translateLabel($label)
    {
        return __($label);
    }

    /**
     * Takes a string and searches for placeholders ({{CONSTANT_NAME}}) to replace with a constant value.
     *
     * @param string $value an option value that may contain a placeholder for a constant value
     * @return mixed|string the value after being replaced by the constant if needed
     */
    private function _fillInConstantPlaceholders($value)
    {
        if (is_string($value) && preg_match('/^{{(\\\\[A-Z][\\\\A-Za-z\d_]+::[A-Z\d_]+)}}$/', $value, $matches)) {
            $value = constant($matches[1]);
        }
        return $value;
    }

    /**
     * Retrieve options list from source model
     *
     * @param string $sourceModel Source model class name or class::method
     * @return array
     */
    protected function _getOptionsFromSourceModel($sourceModel)
    {
        $method = false;
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $sourceModel, $matches)) {
            array_shift($matches);
            list($sourceModel, $method) = array_values($matches);
        }

        $sourceModel = $this->_sourceFactory->create($sourceModel);
        if ($sourceModel instanceof \Magento\Object) {
            $sourceModel->setPath($this->getPath());
        }
        if ($method) {
            if ($this->getType() == 'multiselect') {
                $optionArray = $sourceModel->$method();
            } else {
                $optionArray = array();
                foreach ($sourceModel->$method() as $key => $value) {
                    if (is_array($value)) {
                        $optionArray[] = $value;
                    } else {
                        $optionArray[] = array('label' => $value, 'value' => $key);
                    }
                }
            }
        } else {
            $optionArray = $sourceModel->toOptionArray($this->getType() == 'multiselect');
        }
        return $optionArray;
    }

    /**
     * Retrieve field dependencies
     *
     * @param string $fieldPrefix
     * @param string $storeCode
     * @return array
     */
    public function getDependencies($fieldPrefix, $storeCode)
    {
        $dependencies = array();
        if (false == isset($this->_data['depends']['fields'])) {
            return $dependencies;
        }
        $dependencies = $this->_dependencyMapper->getDependencies(
            $this->_data['depends']['fields'],
            $storeCode,
            $fieldPrefix
        );
        return $dependencies;
    }

    /**
     * Check whether element should be displayed for advanced users
     *
     * @return bool
     */
    public function isAdvanced()
    {
        return isset($this->_data['advanced']) && $this->_data['advanced'];
    }
}
