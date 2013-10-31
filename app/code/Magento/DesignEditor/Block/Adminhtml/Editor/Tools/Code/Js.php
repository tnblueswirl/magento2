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

namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code;

/**
 * Block that renders JS tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Js extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Theme\Model\Config\Customization
     */
    protected $_customizationConfig;

    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Theme\Model\Config\Customization $customizationConfig
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Theme\Model\Config\Customization $customizationConfig,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_customizationConfig = $customizationConfig;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code\Js
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'action' => '#',
                'method' => 'post',
            ))
        );
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('js_files', 'Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader');

        $jsConfig = array(
            'name'     => 'js_files_uploader',
            'title'    => __('Select JS Files to Upload'),
            'accept'   => 'application/x-javascript',
            'multiple' => '1',
        );
        if ($this->_customizationConfig->isThemeAssignedToStore($this->_themeContext->getEditableTheme())) {
            $confirmMessage = __('These JavaScript files may change the appearance of your live store(s).'
                . ' Are you sure you want to do this?');
            $jsConfig['onclick'] = "return confirm('{$confirmMessage}');";
        }
        $form->addField('js_files_uploader', 'js_files', $jsConfig);

        parent::_prepareForm();
        return $this;
    }

    /**
     * Return confirmation message for delete action
     *
     * @return string
     */
    public function getConfirmMessageDelete()
    {
        return __('Are you sure you want to delete this JavaScript file?'
            . ' The changes to your theme will not be reversible.');
    }

    /**
     * Get upload js url
     *
     * @return string
     */
    public function getJsUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadjs',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get reorder js url
     *
     * @return string
     */
    public function getJsReorderUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/reorderjs',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get delete js url
     *
     * @return string
     */
    public function getJsDeleteUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/deleteCustomFiles', array(
            'theme_id' => $this->_themeContext->getEditableTheme()->getId()
        ));
    }

    /**
     * Get custom js files
     *
     * @return \Magento\Core\Model\Resource\Theme\File\Collection
     */
    public function getFiles()
    {
        $customization = $this->_themeContext->getStagingTheme()->getCustomization();
        $jsFiles = $customization->getFilesByType(\Magento\Core\Model\Theme\Customization\File\Js::TYPE);
        return $this->helper('Magento\Core\Helper\Data')->jsonEncode($customization->generateFileInfo($jsFiles));
    }

    /**
     * Get js tab title
     *
     * @return string
     */
    public function getTitle()
    {
        return __('Custom javascript files');
    }
}
