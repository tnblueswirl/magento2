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
 * Block that renders tabs
 *
 * @method bool getIsActive()
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Tabs;

abstract class AbstractTabs extends \Magento\Core\Block\Template
{
    /**
     * Alias of tab handle block in layout
     */
    const TAB_HANDLE_BLOCK_ALIAS = 'tab_handle';

    /**
     * Alias of tab body block in layout
     */
    const TAB_BODY_BLOCK_ALIAS = 'tab_body';

    /**
     * Tab HTML identifier
     */
    protected $_htmlId;

    /**
     * Tab HTML title
     */
    protected $_title;

    /**
     * Get HTML identifier
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->_htmlId;
    }

    /**
     * Get translated title
     *
     * @return string
     */
    public function getTitle()
    {
        return __($this->_title);
    }

    /**
     * Get tabs html
     *
     * @return array
     */
    public function getTabContents()
    {
        $contents = array();
        /** @var $tabBodyBlock \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Tabs\Body */
        $tabBodyBlock = $this->getChildBlock(self::TAB_BODY_BLOCK_ALIAS);
        foreach ($this->getTabs() as $tab) {
            $contents[] = $tabBodyBlock->setContentBlock($tab['content_block'])
                ->setIsActive($tab['is_active'])
                ->setTabId($tab['id'])
                ->toHtml();
        }
        return $contents;
    }

    /**
     * Get tabs handles
     *
     * @return array
     */
    public function getTabHandles()
    {
        /** @var $tabHandleBlock \Magento\Backend\Block\Template */
        $tabHandleBlock = $this->getChildBlock(self::TAB_HANDLE_BLOCK_ALIAS);
        $handles = array();
        foreach ($this->getTabs() as $tab) {
            $href = '#' . $tab['id'];
            $handles[] = $tabHandleBlock->setIsActive($tab['is_active'])
                ->setHref($href)
                ->setTitle($tab['title'])
                ->toHtml();
        }

        return $handles;
    }

    /**
     * Get tabs data
     *
     * @return array
     */
    abstract public function getTabs();
}
