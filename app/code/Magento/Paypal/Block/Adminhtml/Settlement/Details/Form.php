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
 * @package     Magento_Paypal
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Settlement reports transaction details
 */
namespace Magento\Paypal\Block\Adminhtml\Settlement\Details;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Paypal\Model\Report\Settlement
     */
    protected $_settlement;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Paypal\Model\Report\Settlement $settlement
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Paypal\Model\Report\Settlement $settlement,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_settlement = $settlement;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare read-only data and group it by fieldsets
     *
     * @return \Magento\Paypal\Block\Adminhtml\Settlement\Details\Form
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Paypal\Model\Report\Settlement\Row */
        $model = $this->_coreRegistry->registry('current_transaction');
        $coreHelper = $this->helper('Magento\Core\Helper\Data');
        $fieldsets = array(
            'reference_fieldset' => array(
                'fields' => array(
                    'transaction_id' => array('label' => $this->_settlement->getFieldLabel('transaction_id')),
                    'invoice_id' => array('label' => $this->_settlement->getFieldLabel('invoice_id')),
                    'paypal_reference_id' => array('label' => $this->_settlement->getFieldLabel('paypal_reference_id')),
                    'paypal_reference_id_type' => array(
                        'label' => $this->_settlement->getFieldLabel('paypal_reference_id_type'),
                        'value' => $model->getReferenceType($model->getData('paypal_reference_id_type'))
                    ),
                    'custom_field' => array('label' => $this->_settlement->getFieldLabel('custom_field')),
                ),
                'legend' => __('Reference Information')
            ),
            'transaction_fieldset' => array(
                'fields' => array(
                    'transaction_event_code' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_event_code'),
                        'value' => sprintf('%s (%s)',
                            $model->getData('transaction_event_code'),
                            $model->getTransactionEvent($model->getData('transaction_event_code'))
                        )
                    ),
                    'transaction_initiation_date' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_initiation_date'),
                        'value' => $coreHelper->formatDate(
                            $model->getData('transaction_initiation_date'),
                            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                            true
                        )
                    ),
                    'transaction_completion_date' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_completion_date'),
                        'value' => $coreHelper->formatDate(
                            $model->getData('transaction_completion_date'),
                            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                            true
                        )
                    ),
                    'transaction_debit_or_credit' => array(
                        'label' => $this->_settlement->getFieldLabel('transaction_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('transaction_debit_or_credit'))
                    ),
                    'gross_transaction_amount' => array(
                        'label' => $this->_settlement->getFieldLabel('gross_transaction_amount'),
                        'value' => $this->_locale->currency($model->getData('gross_transaction_currency'))
                            ->toCurrency($model->getData('gross_transaction_amount'))
                    ),
                ),
                'legend' => __('Transaction Information')
            ),
            'fee_fieldset' => array(
                'fields' => array(
                    'fee_debit_or_credit' => array(
                        'label' => $this->_settlement->getFieldLabel('fee_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('fee_debit_or_credit'))
                    ),
                    'fee_amount' => array(
                        'label' => $this->_settlement->getFieldLabel('fee_amount'),
                        'value' => $this->_locale->currency($model->getData('fee_currency'))
                            ->toCurrency($model->getData('fee_amount'))
                    ),
                ),
                'legend' => __('PayPal Fee Information')
            ),
        );

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                $fieldset->addField($id, 'label', array(
                    'name'  => $id,
                    'label' => $info['label'],
                    'title' => $info['label'],
                    'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                ));
            }
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
