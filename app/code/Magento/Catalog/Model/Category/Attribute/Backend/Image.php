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
 * @package     Magento_Catalog
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog category image attribute backend model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Category\Attribute\Backend;

class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * Dir model
     *
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * File Uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\App\Dir $dir
     * @param \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\App\Dir $dir,
        \Magento\Core\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_dir = $dir;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($logger);
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Object $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName() . '_additional_data');

        // if no image was set - nothing to do
        if (empty($value) && empty($_FILES)) {
            return $this;
        }

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = $this->_dir->getDir(\Magento\App\Dir::MEDIA) . DS . 'catalog' . DS . 'category' . DS;

        try {
            /** @var $uploader \Magento\Core\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(array('fileId' => $this->getAttribute()->getName()));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Core\Model\File\Uploader::TMP_NAME_EMPTY) {
                $this->_logger->logException($e);
            }
        }
        return $this;
    }
}
