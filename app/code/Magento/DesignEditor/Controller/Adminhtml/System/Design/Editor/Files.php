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
 * @package     Magento_DesignEditor
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Files controller
 */

namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor;

class Files
    extends \Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files
{
    /**
     * Tree json action
     */
    public function treeJsonAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Tree')
                    ->getTreeJson($this->_getStorage()->getTreeArray())
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array()));
        }
    }

    /**
     * Contents action
     */
    public function contentsAction()
    {
        try {
            $this->loadLayout('empty');
            $this->getLayout()->getBlock('editor_files.files')->setStorage($this->_getStorage());
            $this->renderLayout();

            $this->_getSession()->setStoragePath(
                $this->_objectManager->get('Magento\Theme\Helper\Storage')->getCurrentPath()
            );
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }
}
