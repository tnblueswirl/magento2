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
 * Block that renders Custom tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Custom extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Upload file element html id
     */
    const FILE_ELEMENT_NAME = 'css_file_uploader';

    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_themeContext = $themeContext;
    }


    /**
     * Create a form element with necessary controls
     *
     * @return \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'action'   => '#',
                'method'   => 'post',
            ))
        );
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('css_file', 'Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader');

        $form->addField($this->getFileElementName(), 'css_file', array(
            'name'     => $this->getFileElementName(),
            'accept'   => 'text/css',
            'no_span'  => true
        ));

        parent::_prepareForm();
        return $this;
    }

    /**
     * Get url to download custom CSS file
     *
     * @return string
     */
    public function getDownloadCustomCssUrl()
    {
        return $this->getUrl('*/system_design_theme/downloadCustomCss',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to upload custom CSS file
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/upload',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to save custom CSS file
     *
     * @return string
     */
    public function getSaveCustomCssUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveCssContent',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get theme custom css content
     *
     * @param string $targetElementId
     * @param string $contentType
     * @return string
     */
    public function getMediaBrowserUrl($targetElementId, $contentType)
    {
        return $this->getUrl('*/system_design_editor_files/index', array(
            'target_element_id'                           => $targetElementId,
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => $this->_themeContext->getEditableTheme()->getId(),
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => $contentType
        ));
    }

    /**
     * Get theme file (with custom CSS)
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return \Magento\Core\Model\Theme\FileInterface|null
     */
    protected function _getCustomCss($theme)
    {
        $files = $theme->getCustomization()->getFilesByType(
            \Magento\Theme\Model\Theme\Customization\File\CustomCss::TYPE
        );
        return reset($files);
    }

    /**
     * Get theme custom CSS content
     *
     * @return null|string
     */
    public function getCustomCssContent()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getContent() : null;
    }

    /**
     * Get custom CSS file name
     *
     * @return string|null
     */
    public function getCustomFileName()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getFileName() : null;
    }

    /**
     * Get file element name
     *
     * @return string
     */
    public function getFileElementName()
    {
        return self::FILE_ELEMENT_NAME;
    }
}
