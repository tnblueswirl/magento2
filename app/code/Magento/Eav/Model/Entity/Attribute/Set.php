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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav attribute set model
 *
 * @method \Magento\Eav\Model\Resource\Entity\Attribute\Set getResource()
 * @method int getEntityTypeId()
 * @method \Magento\Eav\Model\Entity\Attribute\Set setEntityTypeId(int $value)
 * @method string getAttributeSetName()
 * @method \Magento\Eav\Model\Entity\Attribute\Set setAttributeSetName(string $value)
 * @method int getSortOrder()
 * @method \Magento\Eav\Model\Entity\Attribute\Set setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute;

class Set extends \Magento\Core\Model\AbstractModel
{
    /**
     * Resource instance
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Set
     */
    protected $_resource;

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'eav_entity_attribute_set';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory
     */
    protected $_attrGroupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute
     */
    protected $_resourceAttribute;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\GroupFactory $attrGroupFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute $resourceAttribute
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\Attribute\GroupFactory $attrGroupFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute $resourceAttribute,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_eavConfig = $eavConfig;
        $this->_attrGroupFactory = $attrGroupFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_resourceAttribute = $resourceAttribute;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Resource\Entity\Attribute\Set');
    }

    /**
     * Init attribute set from skeleton (another attribute set)
     *
     * @param int $skeletonId
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    public function initFromSkeleton($skeletonId)
    {
        $groups = $this->_attrGroupFactory->create()
            ->getResourceCollection()
            ->setAttributeSetFilter($skeletonId)
            ->load();

        $newGroups = array();
        foreach ($groups as $group) {
            $newGroup = clone $group;
            $newGroup->setId(null)
                ->setAttributeSetId($this->getId())
                ->setDefaultId($group->getDefaultId());

            $groupAttributesCollection = $this->_attributeFactory->create()
                ->getResourceCollection()
                ->setAttributeGroupFilter($group->getId())
                ->load();

            $newAttributes = array();
            foreach ($groupAttributesCollection as $attribute) {
                $newAttribute = $this->_attributeFactory->create()
                    ->setId($attribute->getId())
                    //->setAttributeGroupId($newGroup->getId())
                    ->setAttributeSetId($this->getId())
                    ->setEntityTypeId($this->getEntityTypeId())
                    ->setSortOrder($attribute->getSortOrder());
                $newAttributes[] = $newAttribute;
            }
            $newGroup->setAttributes($newAttributes);
            $newGroups[] = $newGroup;
        }
        $this->setGroups($newGroups);

        return $this;
    }

    /**
     * Collect data for save
     *
     * @param array $data
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    public function organizeData($data)
    {
        $modelGroupArray = array();
        $modelAttributeArray = array();
        $attributeIds = array();
        if ($data['attributes']) {
            $ids = array();
            foreach ($data['attributes'] as $attribute) {
                $ids[] = $attribute[0];
            }
            $attributeIds = $this->_resourceAttribute->getValidAttributeIds($ids);
        }
        if ($data['groups']) {
            foreach ($data['groups'] as $group) {
                $modelGroup = $this->_attrGroupFactory->create();
                $modelGroup->setId(is_numeric($group[0]) && $group[0] > 0 ? $group[0] : null)
                    ->setAttributeGroupName($group[1])
                    ->setAttributeSetId($this->getId())
                    ->setSortOrder($group[2]);

                if ($data['attributes']) {
                    foreach ($data['attributes'] as $attribute) {
                        if ($attribute[1] == $group[0] && in_array($attribute[0], $attributeIds)) {
                            $modelAttribute = $this->_attributeFactory->create();
                            $modelAttribute->setId($attribute[0])
                                ->setAttributeGroupId($attribute[1])
                                ->setAttributeSetId($this->getId())
                                ->setEntityTypeId($this->getEntityTypeId())
                                ->setSortOrder($attribute[2]);
                            $modelAttributeArray[] = $modelAttribute;
                        }
                    }
                    $modelGroup->setAttributes($modelAttributeArray);
                    $modelAttributeArray = array();
                }
                $modelGroupArray[] = $modelGroup;
            }
            $this->setGroups($modelGroupArray);
        }


        if ($data['not_attributes']) {
            $modelAttributeArray = array();
            foreach ($data['not_attributes'] as $attributeId) {
                $modelAttribute = $this->_attributeFactory->create();

                $modelAttribute->setEntityAttributeId($attributeId);
                $modelAttributeArray[] = $modelAttribute;
            }
            $this->setRemoveAttributes($modelAttributeArray);
        }

        if ($data['removeGroups']) {
            $modelGroupArray = array();
            foreach ($data['removeGroups'] as $groupId) {
                $modelGroup = $this->_attrGroupFactory->create();
                $modelGroup->setId($groupId);

                $modelGroupArray[] = $modelGroup;
            }
            $this->setRemoveGroups($modelGroupArray);
        }
        $this->setAttributeSetName($data['attribute_set_name'])
            ->setEntityTypeId($this->getEntityTypeId());

        return $this;
    }

    /**
     * Validate attribute set name
     *
     * @return bool
     * @throws \Magento\Eav\Exception
     */
    public function validate()
    {
        $attributeSetName = $this->getAttributeSetName();
        if ($attributeSetName == '') {
            throw new \Magento\Eav\Exception(
                __('Attribute set name is empty.')
            );
        }

        if (!$this->_getResource()->validate($this, $attributeSetName)) {
            throw new \Magento\Eav\Exception(
                __('An attribute set with the "%1" name already exists.', $attributeSetName)
            );
        }

        return true;
    }

    /**
     * Add set info to attributes
     *
     * @param string|\Magento\Eav\Model\Entity\Type $entityType
     * @param array $attributes
     * @param int $setId
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    public function addSetInfo($entityType, array $attributes, $setId = null)
    {
        $attributeIds   = array();
        $entityType     = $this->_eavConfig->getEntityType($entityType);
        foreach ($attributes as $attribute) {
            $attribute = $this->_eavConfig->getAttribute($entityType, $attribute);
            if ($setId && is_array($attribute->getAttributeSetInfo($setId))) {
                continue;
            }
            if (!$attribute->getAttributeId()) {
                continue;
            }
            $attributeIds[] = $attribute->getAttributeId();
        }

        if ($attributeIds) {
            $setInfo = $this->_getResource()
                ->getSetInfo($attributeIds, $setId);

            foreach ($attributes as $attribute) {
                $attribute = $this->_eavConfig->getAttribute($entityType, $attribute);
                if (!$attribute->getAttributeId()) {
                    continue;
                }
                if (!in_array($attribute->getAttributeId(), $attributeIds)) {
                    continue;
                }
                if (is_numeric($setId)) {
                    $attributeSetInfo = $attribute->getAttributeSetInfo();
                    if (!is_array($attributeSetInfo)) {
                        $attributeSetInfo = array();
                    }
                    if (isset($setInfo[$attribute->getAttributeId()][$setId])) {
                        $attributeSetInfo[$setId] = $setInfo[$attribute->getAttributeId()][$setId];
                    }
                    $attribute->setAttributeSetInfo($attributeSetInfo);
                } else {
                    if (isset($setInfo[$attribute->getAttributeId()])) {
                        $attribute->setAttributeSetInfo($setInfo[$attribute->getAttributeId()]);
                    } else {
                        $attribute->setAttributeSetInfo(array());
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Return default Group Id for current or defined Attribute Set
     *
     * @param int $setId
     * @return int|null
     */
    public function getDefaultGroupId($setId = null)
    {
        if ($setId === null) {
            $setId = $this->getId();
        }

        return $setId ? $this->_getResource()->getDefaultGroupId($setId) : null;
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _getResource()
    {
        return $this->_resource ?: parent::_getResource();
    }
}
