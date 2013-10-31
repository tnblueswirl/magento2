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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Images content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms\Wysiwyg\Images;

class Content extends \Magento\Adminhtml\Block\Widget\Container
{
    /**
     * Block construction
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_headerText = __('Media Storage');
        $this->_removeButton('back')->_removeButton('edit');
        $this->_addButton('new_folder', array(
            'class'   => 'save',
            'label'   => __('Create Folder...'),
            'type'    => 'button',
        ));

        $this->_addButton('delete_folder', array(
            'class'   => 'delete no-display',
            'label'   => __('Delete Folder'),
            'type'    => 'button',
        ));

        $this->_addButton('delete_files', array(
            'class'   => 'delete no-display',
            'label'   => __('Delete File'),
            'type'    => 'button',
        ));

        $this->_addButton('insert_files', array(
            'class'   => 'save no-display primary',
            'label'   => __('Insert File'),
            'type'    => 'button',
        ));
    }

    /**
     * Files action source URL
     *
     * @return string
     */
    public function getContentsUrl()
    {
        return $this->getUrl('*/*/contents', array('type' => $this->getRequest()->getParam('type')));
    }

    /**
     * Javascript setup object for filebrowser instance
     *
     * @return string
     */
    public function getFilebrowserSetupObject()
    {
        $setupObject = new \Magento\Object();

        $setupObject->setData(array(
            'newFolderPrompt'                 => __('New Folder Name:'),
            'deleteFolderConfirmationMessage' => __('Are you sure you want to delete this folder?'),
            'deleteFileConfirmationMessage'   => __('Are you sure you want to delete this file?'),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl'     => $this->getContentsUrl(),
            'onInsertUrl'     => $this->getOnInsertUrl(),
            'newFolderUrl'    => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl'  => $this->getDeleteFilesUrl(),
            'headerText'      => $this->getHeaderText(),
            'showBreadcrumbs' => true
        ));

        return $this->_coreData->jsonEncode($setupObject);
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getNewfolderUrl()
    {
        return $this->getUrl('*/*/newFolder');
    }

    /**
     * Delete directory action target URL
     *
     * @return string
     */
    protected function getDeletefolderUrl()
    {
        return $this->getUrl('*/*/deleteFolder');
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function getDeleteFilesUrl()
    {
        return $this->getUrl('*/*/deleteFiles');
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getOnInsertUrl()
    {
        return $this->getUrl('*/*/onInsert');
    }

    /**
     * Target element ID getter
     *
     * @return string
     */
    public function getTargetElementId()
    {
        return $this->getRequest()->getParam('target_element_id');
    }
}
