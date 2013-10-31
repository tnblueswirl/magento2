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
 * @category   Magento
 * @package    Magento_Data
 * @copyright  Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract class for form, coumn and fieldset
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form;

class AbstractForm extends \Magento\Object
{
    /**
     * Form level elements collection
     *
     * @var \Magento\Data\Form\Element\Collection
     */
    protected $_elements;

    /**
     * Element type classes
     *
     * @var array
     */
    protected $_types = array();

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_factoryElement;

    /**
     * @var \Magento\Data\Form\Element\CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        $this->_factoryElement = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
        parent::__construct($attributes);
        $this->_construct();
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     */
    protected function _construct()
    {
    }

    /**
     * Add element type
     *
     * @param string $type
     * @param string $className
     * @return \Magento\Data\Form\AbstractForm
     */
    public function addType($type, $className)
    {
        $this->_types[$type] = $className;
        return $this;
    }

    /**
     * Get elements collection
     *
     * @return \Magento\Data\Form\Element\Collection
     */
    public function getElements()
    {
        if (empty($this->_elements)) {
            $this->_elements = $this->_factoryCollection->create(array('container' => $this));
        }
        return $this->_elements;
    }

    /**
     * Disable elements
     *
     * @param boolean $readonly
     * @param boolean $useDisabled
     * @return \Magento\Data\Form\AbstractForm
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        if ($useDisabled) {
            $this->setDisabled($readonly);
            $this->setData('readonly_disabled', $readonly);
        } else {
            $this->setData('readonly', $readonly);
        }
        foreach ($this->getElements() as $element) {
            $element->setReadonly($readonly, $useDisabled);
        }

        return $this;
    }

    /**
     * Add form element
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @param bool|string|null $after
     *
     * @return \Magento\Data\Form
     */
    public function addElement(\Magento\Data\Form\Element\AbstractElement $element, $after = null)
    {
        $element->setForm($this);
        $this->getElements()->add($element, $after);
        return $this;
    }

    /**
     * Add child element
     *
     * if $after parameter is false - then element adds to end of collection
     * if $after parameter is null - then element adds to befin of collection
     * if $after parameter is string - then element adds after of the element with some id
     *
     * @param   string $elementId
     * @param   string $type
     * @param   array  $config
     * @param   mixed  $after
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        if (isset($this->_types[$type])) {
            $type = $this->_types[$type];
        }
        $element = $this->_factoryElement->create($type, array('attributes' => $config));
        $element->setId($elementId);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * Enter description here...
     *
     * @param string $elementId
     * @return \Magento\Data\Form\AbstractForm
     */
    public function removeField($elementId)
    {
        $this->getElements()->remove($elementId);
        return $this;
    }

    /**
     * Add fieldset
     *
     * @param string $elementId
     * @param array $config
     * @param bool|string|null $after
     * @param bool $isAdvanced
     * @return \Magento\Data\Form\Element\Fieldset
     */
    public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
    {
        $element = $this->_factoryElement->create('fieldset', array('attributes' => $config));
        $element->setId($elementId);
        $element->setAdvanced($isAdvanced);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * Add column element
     *
     * @param string $elementId
     * @param array $config
     * @return \Magento\Data\Form\Element\Column
     */
    public function addColumn($elementId, $config)
    {
        $element = $this->_factoryElement->create('column', array('attributes' => $config));
        $element->setForm($this)
            ->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    /**
     * Convert elements to array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function convertToArray(array $arrAttributes = array())
    {
        $res = array();
        $res['config']  = $this->getData();
        $res['formElements']= array();
        foreach ($this->getElements() as $element) {
            $res['formElements'][] = $element->toArray();
        }
        return $res;
    }

}
