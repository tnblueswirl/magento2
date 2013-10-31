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

/**
 * File storage helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper\File;

class Storage extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Current storage code
     *
     * @var int
     */
    protected $_currentStorage = null;

    /**
     * List of internal storages
     *
     * @var array
     */
    protected $_internalStorageList = array(
        \Magento\Core\Model\File\Storage::STORAGE_MEDIA_FILE_SYSTEM
    );

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * @var \Magento\Core\Model\File\Storage
     */
    protected $_storage;

    /**
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\File\Storage $storage
     */
    public function __construct(
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\File\Storage $storage
    ) {
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_storage = $storage;
        parent::__construct($context);
    }

    /**
     * Return saved storage code
     *
     * @return int
     */
    public function getCurrentStorageCode()
    {
        if (is_null($this->_currentStorage)) {
            $this->_currentStorage = (int) $this->_app
                ->getConfig()->getValue(\Magento\Core\Model\File\Storage::XML_PATH_STORAGE_MEDIA, 'default');
        }

        return $this->_currentStorage;
    }

    /**
     * Retrieve file system storage model
     *
     * @return \Magento\Core\Model\File\Storage\File
     */
    public function getStorageFileModel()
    {
        return $objectManager = \Magento\Core\Model\ObjectManager::getInstance()
            ->getSingleton('Magento\Core\Model\File\Storage\File');
    }

    /**
     * Check if storage is internal
     *
     * @param  int|null $storage
     * @return bool
     */
    public function isInternalStorage($storage = null)
    {
        $storage = (!is_null($storage)) ? (int) $storage : $this->getCurrentStorageCode();

        return in_array($storage, $this->_internalStorageList);
    }

    /**
     * Retrieve storage model
     *
     * @param  int|null $storage
     * @param  array $params
     * @return \Magento\Core\Model\AbstractModel|bool
     */
    public function getStorageModel($storage = null, $params = array())
    {
        return $this->_storage->getStorageModel($storage, $params);
    }

    /**
     * Check if needed to copy file from storage to file system and
     * if file exists in the storage
     *
     * @param  string $filename
     * @return bool|int
     */
    public function processStorageFile($filename)
    {
        if ($this->isInternalStorage()) {
            return false;
        }

        $dbHelper = $this->_coreFileStorageDb;

        $relativePath = $dbHelper->getMediaRelativePath($filename);
        $file = $this->getStorageModel()->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        return $this->saveFileToFileSystem($file);
    }

    /**
     * Save file to file system
     *
     * @param  \Magento\Core\Model\File\Storage\Database $file
     * @return bool|int
     */
    public function saveFileToFileSystem($file)
    {
        return $this->getStorageFileModel()->saveFile($file, true);
    }

}
