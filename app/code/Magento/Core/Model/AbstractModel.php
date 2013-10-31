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
 * @package     Magento_Core
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Core\Model;

/**
 * Abstract model class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractModel extends \Magento\Object
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_abstract';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * Resource model instance
     *
     * @var \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected $_resource;

    /**
     * Resource collection
     *
     * @var \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_resourceCollection;
    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Name of the resource collection model
     *
     * @var string
     */
    protected $_collectionName;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var string|array|true
     */
    protected $_cacheTag = false;

    /**
     * Flag which can stop data saving after before save
     * Can be used for next sequence: we check data in _beforeSave, if data are
     * not valid - we can set this flag to false value and save process will be stopped
     *
     * @var bool
     */
    protected $_dataSaveAllowed = true;

    /**
     * Flag which allow detect object state: is it new object (without id) or existing one (with id)
     *
     * @var bool
     */
    protected $_isObjectNew = null;

    /**
     * Validator for checking the model state before saving it
     *
     * @var \Zend_Validate_Interface|bool|null
     */
    protected $_validatorBeforeSave = null;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * Application Cache Manager
     *
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_eventDispatcher = $context->getEventDispatcher();
        $this->_cacheManager = $context->getCacheManager();
        $this->_resource = $resource;
        $this->_resourceCollection = $resourceCollection;
        $this->_logger = $context->getLogger();

        if (method_exists($this->_resource, 'getIdFieldName') || $this->_resource instanceof \Magento\Object) {
            $this->_idFieldName = $this->_getResource()->getIdFieldName();
        }

        parent::__construct($data);
        $this->_construct();
    }

    /**
     * Model construct that should be used for object initialization
     */
    protected function _construct()
    {
    }

    /**
     * Standard model initialization
     *
     * @param string $resourceModel
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _init($resourceModel)
    {
        $this->_setResourceModel($resourceModel);
        $this->_idFieldName = $this->_getResource()->getIdFieldName();
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        $properties = array_keys(get_object_vars($this));
        $properties = array_diff($properties, array('_eventDispatcher', '_cacheManager', '_coreRegistry'));
        return $properties;
    }

    /**
     * Init not serializable fields
     */
    public function __wakeup()
    {
        $objectManager = \Magento\Core\Model\ObjectManager::getInstance();
        $this->_eventDispatcher = $objectManager->get('Magento\Event\ManagerInterface');
        $this->_cacheManager = $objectManager->get('Magento\Core\Model\CacheInterface');
        $this->_coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
    }

    /**
     * Set resource names
     *
     * If collection name is omitted, resource name will be used with _collection appended
     *
     * @param string $resourceName
     * @param string|null $collectionName
     */
    protected function _setResourceModel($resourceName, $collectionName = null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($collectionName)) {
            $collectionName = $resourceName . \Magento\Autoload\IncludePath::NS_SEPARATOR . 'Collection';
        }
        $this->_collectionName = $collectionName;
    }

    /**
     * Get resource instance
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _getResource()
    {
        if (empty($this->_resourceName) && empty($this->_resource)) {
            throw new \Magento\Core\Exception(__('Resource is not set.'));
        }

        return $this->_resource ?: \Magento\Core\Model\ObjectManager::getInstance()->get($this->_resourceName);
    }

    /**
     * Retrieve model resource name
     *
     * @return string
     */
    public function getResourceName()
    {
        return ($this->_resource) ? get_class($this->_resource) : ($this->_resourceName ? $this->_resourceName : null);
    }

    /**
     * Get collection instance
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollection) && empty($this->_collectionName)) {
            throw new \Magento\Core\Exception(
                __('Model collection resource name is not defined.')
            );
        }
        return $this->_resourceCollection
            ? clone $this->_resourceCollection
            : \Magento\Core\Model\ObjectManager::getInstance()->create(
                $this->_collectionName, array('resource' => $this->_getResource())
            );
    }

    /**
     * Retrieve collection instance
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->getResourceCollection();
    }

    /**
     * Load object data
     *
     * @param integer $modelId
     * @param null|string $field
     * @return \Magento\Core\Model\AbstractModel
     */
    public function load($modelId, $field = null)
    {
        $this->_beforeLoad($modelId, $field);
        $this->_getResource()->load($this, $modelId, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Get array of objects transferred to default events processing
     *
     * @return array
     */
    protected function _getEventData()
    {
        return array(
            'data_object'       => $this,
            $this->_eventObject => $this,
        );
    }

    /**
     * Processing object before load data
     *
     * @param int $modelId
     * @param null|string $field
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeLoad($modelId, $field = null)
    {
        $params = array('object' => $this, 'field' => $field, 'value' => $modelId);
        $this->_eventDispatcher->dispatch('model_load_before', $params);
        $params = array_merge($params, $this->_getEventData());
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_load_before', $params);
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterLoad()
    {
        $this->_eventDispatcher->dispatch('model_load_after', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_load_after', $this->_getEventData());
        return $this;
    }

    /**
     * Object after load processing. Implemented as public interface for supporting objects after load in collections
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function afterLoad()
    {
        $this->getResource()->afterLoad($this);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Check whether model has changed data.
     * Can be overloaded in child classes to perform advanced check whether model needs to be saved
     * e.g. usign resouceModel->hasDataChanged() or any other technique
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        return $this->hasDataChanges();
    }

    /**
     * Save object data
     *
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Exception
     */
    public function save()
    {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }
        if (!$this->_hasModelChanged()) {
            return $this;
        }
        $this->_getResource()->beginTransaction();
        try {
            $this->_validateBeforeSave();
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
            $this->_getResource()->addCommitCallback(array($this, 'afterCommitCallback'))
                ->commit();
            $this->_hasDataChanges = false;
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        return $this;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function afterCommitCallback()
    {
        $this->_eventDispatcher->dispatch('model_save_commit_after', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_save_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save object has id and we can't detect what object was
     * created in this transaction
     *
     * @param bool|null $flag
     * @return bool
     */
    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }
        $this->_eventDispatcher->dispatch('model_save_before', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_save_before', $this->_getEventData());
        return $this;
    }

    /**
     * Validate model before saving it
     *
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Magento\Core\Exception
     */
    protected function _validateBeforeSave()
    {
        $validator = $this->_getValidatorBeforeSave();
        if ($validator && !$validator->isValid($this)) {
            $errors = $validator->getMessages();
            $exception = new \Magento\Core\Exception(implode(PHP_EOL, $errors));
            foreach ($errors as $errorMessage) {
                $exception->addMessage(new \Magento\Core\Model\Message\Error($errorMessage));
            }
            throw $exception;
        }
        return $this;
    }

    /**
     * Returns validator, which contains all rules to validate this model.
     * Returns FALSE, if no validation rules exist.
     *
     * @return \Zend_Validate_Interface|false
     */
    protected function _getValidatorBeforeSave()
    {
        if ($this->_validatorBeforeSave === null) {
            $this->_validatorBeforeSave = $this->_createValidatorBeforeSave();
        }
        return $this->_validatorBeforeSave;
    }

    /**
     * Creates validator for the model with all validation rules in it.
     * Returns FALSE, if no validation rules exist.
     *
     * @return \Zend_Validate_Interface|bool
     */
    protected function _createValidatorBeforeSave()
    {
        $modelRules = $this->_getValidationRulesBeforeSave();
        $resourceRules = $this->_getResource()->getValidationRulesBeforeSave();
        if (!$modelRules && !$resourceRules) {
            return false;
        }

        if ($modelRules && $resourceRules) {
            $validator = new \Zend_Validate();
            $validator->addValidator($modelRules);
            $validator->addValidator($resourceRules);
        } elseif ($modelRules) {
            $validator = $modelRules;
        } else {
            $validator = $resourceRules;
        }

        return $validator;
    }

    /**
     * Template method to return validate rules for the entity
     *
     * @return \Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        return null;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array|bool
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
                }
                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }
        return $tags;
    }

    /**
     * Get cache tags associated with object id
     *
     * @return array
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = array();
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $_tag) {
                    $tags[] = $_tag . '_' . $this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag . '_' . $this->getId();
            }
        }
        return $tags;
    }

    /**
     * Remove model object related cache
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function cleanModelCache()
    {
        $tags = $this->getCacheTags();
        if ($tags !== false) {
            $this->_cacheManager->clean($tags);
        }
        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterSave()
    {
        $this->cleanModelCache();
        $this->_eventDispatcher->dispatch('model_save_after', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_save_after', $this->_getEventData());
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return \Magento\Core\Model\AbstractModel
     * @throws \Exception
     */
    public function delete()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();

            $this->_getResource()->commit();
            $this->_afterDeleteCommit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Processing object before delete data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeDelete()
    {
        $this->_eventDispatcher->dispatch('model_delete_before', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Safeguard func that will check, if we are in admin area
     *
     * @throws \Magento\Core\Exception
     */
    protected function _protectFromNonAdmin()
    {
        if ($this->_coreRegistry->registry('isSecureArea')) {
            return;
        }
        /* Store manager does not work well in this place when injected via context */
        if (!\Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Model\StoreManager')->getStore()->isAdmin()
        ) {
            throw new \Magento\Core\Exception(__('Cannot complete this operation from non-admin area.'));
        }
    }

    /**
     * Processing object after delete data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterDelete()
    {
        $this->_eventDispatcher->dispatch('model_delete_after', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_delete_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing manipulation after main transaction commit
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _afterDeleteCommit()
    {
        $this->_eventDispatcher->dispatch('model_delete_commit_after', array('object' => $this));
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_delete_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Retrieve model resource
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function getResource()
    {
        return $this->_getResource();
    }

    /**
     * Retreive entity id
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    /**
     * Clearing object for correct deleting by garbage collector
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    final public function clearInstance()
    {
        $this->_clearReferences();
        $this->_eventDispatcher->dispatch($this->_eventPrefix . '_clear', $this->_getEventData());
        $this->_clearData();
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _clearReferences()
    {
        return $this;
    }

    /**
     * Clearing object's data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _clearData()
    {
        return $this;
    }
}
