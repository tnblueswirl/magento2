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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\User\Block\User\Edit\Tab;

/**
 * Cms page edit form main tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * Locale model
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Construct
     *
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_authSession = $authSession;
        $this->_locale = $locale;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }
    
    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $model = $this->_coreRegistry->registry('permissions_user');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend'=>__('Account Information'))
        );

        if ($model->getUserId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        } else {
            if (! $model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $fieldset->addField('username', 'text', array(
            'name'  => 'username',
            'label' => __('User Name'),
            'id'    => 'username',
            'title' => __('User Name'),
            'required' => true,
        ));

        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => __('First Name'),
            'id'    => 'firstname',
            'title' => __('First Name'),
            'required' => true,
        ));

        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => __('Last Name'),
            'id'    => 'lastname',
            'title' => __('Last Name'),
            'required' => true,
        ));

        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => __('Email'),
            'id'    => 'customer_email',
            'title' => __('User Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
        ));

        $isNewObject = $model->isObjectNew();
        if ($isNewObject) {
            $passwordLabel = __('Password');
        } else {
            $passwordLabel = __('New Password');
        }
        $confirmationLabel = __('Password Confirmation');
        $this->_addPasswordFields($fieldset, $passwordLabel, $confirmationLabel, $isNewObject);

        $fieldset->addField('interface_locale', 'select', array(
            'name'   => 'interface_locale',
            'label'  => __('Interface Locale'),
            'title'  => __('Interface Locale'),
            'values' => $this->_locale->getTranslatedOptionLocales(),
            'class'  => 'select',
        ));

        if ($this->_authSession->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField('is_active', 'select', array(
                'name'      => 'is_active',
                'label'     => __('This account is'),
                'id'        => 'is_active',
                'title'     => __('Account Status'),
                'class'     => 'input-select',
                'style'     => 'width: 80px',
                'options'   => array(
                    '1' => __('Active'),
                    '0' => __('Inactive')
                ),
            ));
        }

        $fieldset->addField('user_roles', 'hidden', array(
            'name' => 'user_roles',
            'id'   => '_user_roles',
        ));

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add password input fields
     *
     * @param \Magento\Data\Form\Element\Fieldset $fieldset
     * @param string $passwordLabel
     * @param string $confirmationLabel
     * @param bool $isRequired
     */
    protected function _addPasswordFields(
        \Magento\Data\Form\Element\Fieldset $fieldset, $passwordLabel, $confirmationLabel, $isRequired = false
    ) {
        $requiredFieldClass = ($isRequired ? ' required-entry' : '');
        $fieldset->addField('password', 'password', array(
            'name'  => 'password',
            'label' => $passwordLabel,
            'id'    => 'customer_pass',
            'title' => $passwordLabel,
            'class' => 'input-text validate-admin-password' . $requiredFieldClass,
            'required' => $isRequired,
        ));
        $fieldset->addField('confirmation', 'password', array(
            'name'  => 'password_confirmation',
            'label' => $confirmationLabel,
            'id'    => 'confirmation',
            'title' => $confirmationLabel,
            'class' => 'input-text validate-cpassword' . $requiredFieldClass,
            'required' => $isRequired,
        ));
    }
}
