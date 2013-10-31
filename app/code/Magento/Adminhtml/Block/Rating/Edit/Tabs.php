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
 * Admin rating left menu
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Rating\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rating Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Rating Information'),
            'title'     => __('Rating Information'),
            'content'   => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tab\Form')->toHtml(),
        ))
        ;
/*
        $this->addTab('answers_section', array(
                'label'     => __('Rating Options'),
                'title'     => __('Rating Options'),
                'content'   => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tab\Options')
                    ->append($this->getLayout()->createBlock('Magento\Adminhtml\Block\Rating\Edit\Tab\Options'))
                    ->toHtml(),
           ));*/
        return parent::_beforeToHtml();
    }
}
