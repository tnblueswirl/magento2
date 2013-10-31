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
 * @package     Magento_ImportExport
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import EAV entity abstract model
 */
namespace Magento\ImportExport\Model\Import\Entity;

abstract class AbstractEav
    extends \Magento\ImportExport\Model\Import\AbstractEntity
{
    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = 'Magento\Data\Collection';

    /**
     * Website manager (currently \Magento\Core\Model\App works as website manager)
     *
     * @var \Magento\Core\Model\App
     */
    protected $_websiteManager;

    /**
     * Store manager (currently \Magento\Core\Model\App works as store manager)
     *
     * @var \Magento\Core\Model\App
     */
    protected $_storeManager;

    /**
     * Entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Attributes with index (not label) value
     *
     * @var array
     */
    protected $_indexValueAttributes = array();

    /**
     * Website code-to-ID
     *
     * @var array
     */
    protected $_websiteCodeToId = array();

    /**
     * All stores code-ID pairs.
     *
     * @var array
     */
    protected $_storeCodeToId = array();

    /**
     * Entity attributes parameters
     *
     *  [attr_code_1] => array(
     *      'options' => array(),
     *      'type' => 'text', 'price', 'textarea', 'select', etc.
     *      'id' => ..
     *  ),
     *  ...
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Attributes collection
     *
     * @var \Magento\Data\Collection
     */
    protected $_attributeCollection;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\App $app
     * @param \Magento\ImportExport\Model\Export\Factory $collectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\Resource\Helper $resourceHelper,
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\App $app,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = array()
    ) {
        parent::__construct(
            $coreData, $coreString, $coreStoreConfig, $importFactory, $resourceHelper, $resource, $data
        );

        $this->_websiteManager = isset($data['website_manager']) ? $data['website_manager'] : $app;
        $this->_storeManager   = isset($data['store_manager']) ? $data['store_manager'] : $app;
        $this->_attributeCollection = isset($data['attribute_collection']) ? $data['attribute_collection']
            : $collectionFactory->create(static::ATTRIBUTE_COLLECTION_NAME);

        if (isset($data['entity_type_id'])) {
            $this->_entityTypeId = $data['entity_type_id'];
        } else {
            $this->_entityTypeId = $eavConfig->getEntityType($this->getEntityTypeCode())->getEntityTypeId();
        }
    }

    /**
     * Retrieve website id by code or false when website code not exists
     *
     * @param $websiteCode
     * @return bool|int
     */
    public function getWebsiteId($websiteCode)
    {
        if (isset($this->_websiteCodeToId[$websiteCode])) {
            return $this->_websiteCodeToId[$websiteCode];
        }

        return false;
    }

    /**
     * Initialize website values
     *
     * @param bool $withDefault
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEav
     */
    protected function _initWebsites($withDefault = false)
    {
        /** @var $website \Magento\Core\Model\Website */
        foreach ($this->_websiteManager->getWebsites($withDefault) as $website) {
            $this->_websiteCodeToId[$website->getCode()] = $website->getId();
        }
        return $this;
    }

    /**
     * Initialize stores data
     *
     * @param bool $withDefault
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEav
     */
    protected function _initStores($withDefault = false)
    {
        /** @var $store \Magento\Core\Model\Store */
        foreach ($this->_storeManager->getStores($withDefault) as $store) {
            $this->_storeCodeToId[$store->getCode()] = $store->getId();
        }
        return $this;
    }

    /**
     * Initialize entity attributes
     *
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEav
     */
    protected function _initAttributes()
    {
        /** @var $attribute \Magento\Eav\Model\Attribute */
        foreach ($this->_attributeCollection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = array(
                'id'          => $attribute->getId(),
                'code'        => $attribute->getAttributeCode(),
                'table'       => $attribute->getBackend()->getTable(),
                'is_required' => $attribute->getIsRequired(),
                'is_static'   => $attribute->isStatic(),
                'rules'       => $attribute->getValidateRules() ? unserialize($attribute->getValidateRules()) : null,
                'type'        => \Magento\ImportExport\Model\Import::getAttributeType($attribute),
                'options'     => $this->getAttributeOptions($attribute)
            );
        }
        return $this;
    }

    /**
     * Entity type ID getter
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->_entityTypeId;
    }

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array $indexAttributes OPTIONAL Additional attribute codes with index values.
     * @return array
     */
    public function getAttributeOptions(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        array $indexAttributes = array()
    ) {
        $options = array();

        if ($attribute->usesSource()) {
            // merge global entity index value attributes
            $indexAttributes = array_merge($indexAttributes, $this->_indexValueAttributes);

            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $indexAttributes) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(\Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    $value = is_array($option['value']) ? $option['value'] : array($option);
                    foreach ($value as $innerOption) {
                        if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                            $options[strtolower($innerOption[$index])] = $innerOption['value'];
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore exceptions connected with source models
            }
        }
        return $options;
    }

    /**
     * Get attribute collection
     *
     * @return \Magento\Data\Collection
     */
    public function getAttributeCollection()
    {
        return $this->_attributeCollection;
    }
}
