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
 * @package     Magento_User
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * User block
 *
 * @category   Magento
 * @package    Magento_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Block;

class User extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Magento\User\Model\Resource\User
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\User\Model\Resource\User $resourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\User\Model\Resource\User $resourceModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_resourceModel = $resourceModel;
    }

    protected function _construct()
    {
        $this->addData(array(
            \Magento\Backend\Block\Widget\Container::PARAM_CONTROLLER => 'user',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BLOCK_GROUP => 'Magento_User',
            \Magento\Backend\Block\Widget\Grid\Container::PARAM_BUTTON_NEW => __('Add New User'),
            \Magento\Backend\Block\Widget\Container::PARAM_HEADER_TEXT => __('Users'),
        ));
        parent::_construct();
        $this->_addNewButton();
    }
}
