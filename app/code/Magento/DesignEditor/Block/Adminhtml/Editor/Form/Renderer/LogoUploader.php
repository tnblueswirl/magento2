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

namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer;

/**
 * Logo uploader element renderer
 *
 * @todo Temporary solution.
 * Discuss logo uploader with PO and remove this method.
 * Logo should be assigned to store view level, but not theme.
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class LogoUploader
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer\ImageUploader
{
    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * @var \Magento\Theme\Model\Config\Customization
     */
    protected $_customization;

    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     *
     * @var array
     */
    protected $_templates = array(
        'Magento_DesignEditor::editor/form/renderer/element/input.phtml',
        'Magento_DesignEditor::editor/form/renderer/logo-uploader.phtml',
    );

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param \Magento\Theme\Model\Config\Customization $customization
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        \Magento\Theme\Model\Config\Customization $customization,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        $this->_customization = $customization;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get logo upload url
     *
     * @param \Magento\Core\Model\Store $store
     * @return string
     */
    public function getLogoUploadUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/uploadStoreLogo',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo upload url
     *
     * @param \Magento\Core\Model\Store $store
     * @return string
     */
    public function getLogoRemoveUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/removeStoreLogo',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo image
     *
     * @param \Magento\Core\Model\Store $store
     * @return string|null
     */
    public function getLogoImage($store)
    {
        $image = null;
        if (null !== $store) {
            $image = basename($this->_storeConfig->getConfig('design/header/logo_src', $store->getId()));
        }
        return $image;
    }

    /**
     * Get stores list
     *
     * @return \Magento\Core\Model\Store|null
     */
    public function getStoresList()
    {
        $stores = $this->_customization->getStoresByThemes();
        return isset($stores[$this->_themeContext->getEditableTheme()->getId()])
            ? $stores[$this->_themeContext->getEditableTheme()->getId()]
            : null;
    }
}
