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
 * Import entity abstract model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model\Import;

abstract class AbstractEntity
{
    /**
     * Custom row import behavior column name
     */
    const COLUMN_ACTION = '_action';

    /**
     * Value in custom column for delete behaviour
     */
    const COLUMN_ACTION_VALUE_DELETE = 'delete';

    /**#@+
     * XML paths to parameters
     */
    const XML_PATH_BUNCH_SIZE = 'import/format_v2/bunch_size';
    const XML_PATH_PAGE_SIZE  = 'import/format_v2/page_size';
    /**#@-*/

    /**#@+
     * Database constants
     */
    const DB_MAX_VARCHAR_LENGTH = 256;
    const DB_MAX_TEXT_LENGTH    = 65536;
    /**#@-*/

    /**
     * DB connection
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Has data process validation done?
     *
     * @var bool
     */
    protected $_dataValidated = false;

    /**
     * DB data source model
     *
     * @var \Magento\ImportExport\Model\Resource\Import\Data
     */
    protected $_dataSourceModel;

    /**
     * Error codes with arrays of corresponding row numbers
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Error counter
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Limit of errors after which pre-processing will exit
     *
     * @var int
     */
    protected $_errorsLimit = 100;

    /**
     * Flag to disable import
     *
     * @var bool
     */
    protected $_importAllowed = true;

    /**
     * Array of invalid rows numbers
     *
     * @var array
     */
    protected $_invalidRows = array();

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * Notice messages
     *
     * @var array
     */
    protected $_notices = array();

    /**
     * Helper to encode/decode json
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Helper to manipulate with string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_stringHelper;

    /**
     * Entity model parameters
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_specialAttributes = array(self::COLUMN_ACTION);

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array();

    /**
     * Number of entities processed by validation
     *
     * @var int
     */
    protected $_processedEntitiesCount = 0;

    /**
     * Number of rows processed by validation
     *
     * @var int
     */
    protected $_processedRowsCount = 0;

    /**
     * Rows which will be skipped during import
     *
     * [Row number 1] => true,
     * ...
     * [Row number N] => true
     *
     * @var array
     */
    protected $_skippedRows = array();

    /**
     * Array of numbers of validated rows as keys and boolean TRUE as values
     *
     * @var array
     */
    protected $_validatedRows = array();

    /**
     * Source model
     *
     * @var \Magento\ImportExport\Model\Import\AbstractSource
     */
    protected $_source;

    /**
     * Array of unique attributes
     *
     * @var array
     */
    protected $_uniqueAttributes = array();

    /**
     * List of available behaviors
     *
     * @var array
     */
    protected $_availableBehaviors = array(
        \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE,
        \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE,
        \Magento\ImportExport\Model\Import::BEHAVIOR_CUSTOM,
    );

    /**
     * Number of items to fetch from db in one query
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Maximum size of packet, that can be sent to DB
     *
     * @var int
     */
    protected $_maxDataSize;

    /**
     * Number of items to save to the db in one query
     *
     * @var int
     */
    protected $_bunchSize;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Model\Resource $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\Resource\Helper $resourceHelper,
        \Magento\Core\Model\Resource $resource,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_dataSourceModel     = isset($data['data_source_model']) ? $data['data_source_model']
            : $importFactory->create()->getDataSourceModel();
        $this->_connection          = isset($data['connection']) ? $data['connection']
            : $resource->getConnection('write');
        $this->_jsonHelper          =  $coreData;
        $this->_stringHelper        =  $coreString;
        $this->_pageSize            = isset($data['page_size']) ? $data['page_size']
            : (static::XML_PATH_PAGE_SIZE ? (int)$this->_coreStoreConfig->getConfig(static::XML_PATH_PAGE_SIZE) : 0);
        $this->_maxDataSize         = isset($data['max_data_size']) ? $data['max_data_size']
            : $resourceHelper->getMaxDataSize();
        $this->_bunchSize           = isset($data['bunch_size']) ? $data['bunch_size']
            : (static::XML_PATH_BUNCH_SIZE ? (int)$this->_coreStoreConfig->getConfig(static::XML_PATH_BUNCH_SIZE) : 0);
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    abstract protected function _importData();

    /**
     * Imported entity type code getter
     *
     * @abstract
     * @return string
     */
    abstract public function getEntityTypeCode();

    /**
     * Change row data before saving in DB table
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        /**
         * Convert all empty strings to null values, as
         * a) we don't use empty string in DB
         * b) empty strings instead of numeric values will product errors in Sql Server
         */
        foreach ($rowData as $key => $val) {
            if ($val === '') {
                $rowData[$key] = null;
            }
        }
        return $rowData;
    }

    /**
     * Validate data rows and save bunches to DB
     *
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    protected function _saveValidatedBunches()
    {
        $source            = $this->getSource();
        $processedDataSize = 0;
        $bunchRows         = array();
        $startNewBunch     = false;
        $nextRowBackup     = array();

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch(
                    $this->getEntityTypeCode(),
                    $this->getBehavior(),
                    $bunchRows
                );

                $bunchRows         = $nextRowBackup;
                $processedDataSize = strlen(serialize($bunchRows));
                $startNewBunch     = false;
                $nextRowBackup     = array();
            }
            if ($source->valid()) {
                // errors limit check
                if ($this->_errorsCount >= $this->_errorsLimit) {
                    return $this;
                }
                $rowData = $source->current();
                // add row to bunch for save
                if ($this->validateRow($rowData, $source->key())) {
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen($this->_jsonHelper->jsonEncode($rowData));

                    $isBunchSizeExceeded = ($this->_bunchSize > 0 && count($bunchRows) >= $this->_bunchSize);

                    if (($processedDataSize + $rowSize) >= $this->_maxDataSize || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = array($source->key() => $rowData);
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $processedDataSize += $rowSize;
                    }
                }
                $this->_processedRowsCount++;
                $source->next();
            }
        }
        return $this;
    }

    /**
     * Add error with corresponding current data source row number
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number
     * @param string $columnName OPTIONAL Column name
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    public function addRowError($errorCode, $errorRowNum, $columnName = null)
    {
        $errorCode = (string)$errorCode;
        $this->_errors[$errorCode][] = array($errorRowNum + 1, $columnName); // one added for human readability
        $this->_invalidRows[$errorRowNum] = true;
        $this->_errorsCount++;

        return $this;
    }

    /**
     * Add message template for specific error code from outside
     *
     * @param string $errorCode Error code
     * @param string $message Message template
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    public function addMessageTemplate($errorCode, $message)
    {
        $this->_messageTemplates[$errorCode] = $message;

        return $this;
    }

    /**
     * Import behavior getter
     *
     * @param array $rowData
     * @return string
     */
    public function getBehavior(array $rowData = null)
    {
        if (isset($this->_parameters['behavior'])
            && in_array($this->_parameters['behavior'], $this->_availableBehaviors)
        ) {
            $behavior = $this->_parameters['behavior'];
            if ($rowData !== null && $behavior == \Magento\ImportExport\Model\Import::BEHAVIOR_CUSTOM) {
                // try analyze value in self::COLUMN_CUSTOM column and return behavior for given $rowData
                if (array_key_exists(self::COLUMN_ACTION, $rowData)) {
                    if (strtolower($rowData[self::COLUMN_ACTION]) == self::COLUMN_ACTION_VALUE_DELETE) {
                        $behavior = \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE;
                    } else {
                        // as per task description, if column value is different to self::COLUMN_CUSTOM_VALUE_DELETE,
                        // we should always use default behavior
                        return self::getDefaultBehavior();
                    }
                    if (in_array($behavior, $this->_availableBehaviors)) {
                        return $behavior;
                    }
                }
            } else {
                // if method is invoked without $rowData we should just return $this->_parameters['behavior']
                return $behavior;
            }
        }

        return self::getDefaultBehavior();
    }

    /**
     * Get default import behavior
     *
     * @return string
     */
    public static function getDefaultBehavior()
    {
        return \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE;
    }

    /**
     * Returns error information grouped by error types and translated (if possible)
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $messages = array();
        foreach ($this->_errors as $errorCode => $errorRows) {
            if (isset($this->_messageTemplates[$errorCode])) {
                $errorCode = (string)__($this->_messageTemplates[$errorCode]);
            }
            foreach ($errorRows as $errorRowData) {
                $key = $errorRowData[1] ? sprintf($errorCode, $errorRowData[1]) : $errorCode;
                $messages[$key][] = $errorRowData[0];
            }
        }
        return $messages;
    }

    /**
     * Returns error counter value
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_errorsCount;
    }

    /**
     * Returns error limit value
     *
     * @return int
     */
    public function getErrorsLimit()
    {
        return $this->_errorsLimit;
    }

    /**
     * Returns invalid rows count
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return count($this->_invalidRows);
    }

    /**
     * Returns model notices
     *
     * @return array
     */
    public function getNotices()
    {
        return $this->_notices;
    }

    /**
     * Returns number of checked entities
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_processedEntitiesCount;
    }

    /**
     * Returns number of checked rows
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_processedRowsCount;
    }

    /**
     * Source object getter
     *
     * @throws \Exception
     * @return \Magento\ImportExport\Model\Import\AbstractSource
     */
    public function getSource()
    {
        if (!$this->_source) {
            throw new \Magento\Core\Exception(__('Source is not set'));
        }
        return $this->_source;
    }

    /**
     * Import process start
     *
     * @return bool Result of operation
     */
    public function importData()
    {
        return $this->_importData();
    }

    /**
     * Is attribute contains particular data (not plain entity attribute)
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isAttributeParticular($attributeCode)
    {
        return in_array($attributeCode, $this->_specialAttributes);
    }

    /**
     * Check one attribute can be overridden in child
     *
     * @param string $attributeCode Attribute code
     * @param array $attributeParams Attribute params
     * @param array $rowData Row data
     * @param int $rowNumber
     * @return boolean
     */
    public function isAttributeValid($attributeCode, array $attributeParams, array $rowData, $rowNumber)
    {
        switch ($attributeParams['type']) {
            case 'varchar':
                $value = $this->_stringHelper->cleanString($rowData[$attributeCode]);
                $valid = $this->_stringHelper->strlen($value) < self::DB_MAX_VARCHAR_LENGTH;
                break;
            case 'decimal':
                $value = trim($rowData[$attributeCode]);
                $valid = ((float)$value == $value) && is_numeric($value);
                break;
            case 'select':
            case 'multiselect':
                $valid = isset($attributeParams['options'][strtolower($rowData[$attributeCode])]);
                break;
            case 'int':
                $value = trim($rowData[$attributeCode]);
                $valid = ((int)$value == $value) && is_numeric($value);
                break;
            case 'datetime':
                $value = trim($rowData[$attributeCode]);
                $valid = strtotime($value) !== false;
                break;
            case 'text':
                $value = $this->_stringHelper->cleanString($rowData[$attributeCode]);
                $valid = $this->_stringHelper->strlen($value) < self::DB_MAX_TEXT_LENGTH;
                break;
            default:
                $valid = true;
                break;
        }

        if (!$valid) {
            $this->addRowError(__("Please correct the value for '%s'."),
                $rowNumber, $attributeCode
            );
        } elseif (!empty($attributeParams['is_unique'])) {
            if (isset($this->_uniqueAttributes[$attributeCode][$rowData[$attributeCode]])) {
                $this->addRowError(__("Duplicate Unique Attribute for '%s'"), $rowNumber, $attributeCode);
                return false;
            }
            $this->_uniqueAttributes[$attributeCode][$rowData[$attributeCode]] = true;
        }
        return (bool) $valid;
    }

    /**
     * Check that is all of data valid
     *
     * @return bool
     */
    public function isDataValid()
    {
        $this->validateData();
        return 0 == $this->getErrorsCount();
    }

    /**
     * Import possibility getter
     *
     * @return bool
     */
    public function isImportAllowed()
    {
        return $this->_importAllowed;
    }

    /**
     * Returns TRUE if row is valid and not in skipped rows array
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    public function isRowAllowedToImport(array $rowData, $rowNumber)
    {
        return $this->validateRow($rowData, $rowNumber) && !isset($this->_skippedRows[$rowNumber]);
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    abstract public function validateRow(array $rowData, $rowNumber);

    /**
     * Set data from outside to change behavior
     *
     * @param array $parameters
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;
        return $this;
    }

    /**
     * Source model setter
     *
     * @param \Magento\ImportExport\Model\Import\AbstractSource $source
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    public function setSource(\Magento\ImportExport\Model\Import\AbstractSource $source)
    {
        $this->_source = $source;
        $this->_dataValidated = false;

        return $this;
    }

    /**
     * Validate data
     *
     * @throws \Exception
     * @return \Magento\ImportExport\Model\Import\AbstractEntity
     */
    public function validateData()
    {
        if (!$this->_dataValidated) {
            // do all permanent columns exist?
            if ($absentColumns = array_diff($this->_permanentAttributes, $this->getSource()->getColNames())) {
                throw new \Magento\Core\Exception(
                    __('Cannot find required columns: %1',
                        implode(', ', $absentColumns)
                    )
                );
            }

            // check attribute columns names validity
            $columnNumber       = 0;
            $emptyHeaderColumns = array();
            $invalidColumns     = array();
            foreach ($this->getSource()->getColNames() as $columnName) {
                $columnNumber++;
                if (!$this->isAttributeParticular($columnName)) {
                    if (trim($columnName) == '') {
                        $emptyHeaderColumns[] = $columnNumber;
                    } elseif (!preg_match('/^[a-z][a-z0-9_]*$/', $columnName)) {
                        $invalidColumns[] = $columnName;
                    }
                }
            }

            if ($emptyHeaderColumns) {
                throw new \Magento\Core\Exception(
                    __('Columns number: "%1" have empty headers',
                        implode('", "', $emptyHeaderColumns)
                    )
                );
            }
            if ($invalidColumns) {
                throw new \Magento\Core\Exception(
                    __('Column names: "%1" are invalid',
                        implode('", "', $invalidColumns)
                    )
                );
            }

            // initialize validation related attributes
            $this->_errors = array();
            $this->_invalidRows = array();
            $this->_saveValidatedBunches();
            $this->_dataValidated = true;
        }
        return $this;
    }
}
