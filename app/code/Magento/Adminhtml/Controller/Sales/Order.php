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
 * Adminhtml sales orders controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Sales;

class Order extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('view', 'index');

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Adminhtml\Controller\Sales\Order
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_order')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Orders'), __('Orders'));
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Model\Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError(__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('sales_order', $order);
        $this->_coreRegistry->register('current_order', $order);
        return $order;
    }

    /**
     * Orders grid
     */
    public function indexAction()
    {
        $this->_title(__('Orders'));
        $this->_initAction()->renderLayout();
    }

    /**
     * Order grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View order detale
     */
    public function viewAction()
    {
        $this->_title(__('Orders'));

        $order = $this->_initOrder();
        if ($order) {
            $this->_initAction();
            $this->_title(sprintf("#%s", $order->getRealOrderId()));
            $this->renderLayout();
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $order->sendNewOrderEmail();
                $historyItem = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Status\History\Collection')
                    ->getUnnotifiedForInstance($order, \Magento\Sales\Model\Order::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }
                $this->_getSession()->addSuccess(__('You sent the order email.'));
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('We couldn\'t send the email order.'));
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            }
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Cancel order
     */
    public function cancelAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $order->cancel()
                    ->save();
                $this->_getSession()->addSuccess(
                    __('You canceled the order.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('You have not canceled the item.'));
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Hold order
     */
    public function holdAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $order->hold()
                    ->save();
                $this->_getSession()->addSuccess(
                    __('You put the order on hold.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('You have not put the order on hold.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Unhold order
     */
    public function unholdAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $order->unhold()
                    ->save();
                $this->_getSession()->addSuccess(
                    __('You released the order from holding status.')
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addError(__('The order was not on hold.'));
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
    }

    /**
     * Manage payment state
     *
     * Either denies or approves a payment that is in "review" state
     */
    public function reviewPaymentAction()
    {
        try {
            $order = $this->_initOrder();
            if (!$order) {
                return;
            }
            $action = $this->getRequest()->getParam('action', '');
            switch ($action) {
                case 'accept':
                    $order->getPayment()->accept();
                    $message = __('The payment has been accepted.');
                    break;
                case 'deny':
                    $order->getPayment()->deny();
                    $message = __('The payment has been denied.');
                    break;
                case 'update':
                    $order->getPayment()
                        ->registerPaymentReviewAction(\Magento\Sales\Model\Order\Payment::REVIEW_ACTION_UPDATE, true);
                    $message = __('The payment update has been made.');
                    break;
                default:
                    throw new \Exception(sprintf('Action "%s" is not supported.', $action));
            }
            $order->save();
            $this->_getSession()->addSuccess($message);
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We couldn\'t update the payment.'));
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
        }
        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * Add order comment action
     */
    public function addCommentAction()
    {
        $order = $this->_initOrder();
        if ($order) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && ($data['status'] == $order->getDataByKey('status'))) {
                    throw new \Magento\Core\Exception(__('Comment text cannot be empty.'));
                }

                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->save();

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }

    /**
     * Generate invoices grid for ajax request
     */
    public function invoicesAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Order\View\Tab\Invoices')->toHtml()
        );
    }

    /**
     * Generate shipments grid for ajax request
     */
    public function shipmentsAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Order\View\Tab\Shipments')->toHtml()
        );
    }

    /**
     * Generate credit memos grid for ajax request
     */
    public function creditmemosAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Order\View\Tab\Creditmemos')->toHtml()
        );
    }

    /**
     * Generate order history for ajax request
     */
    public function commentsHistoryAction()
    {
        $this->_initOrder();
        $html = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Sales\Order\View\Tab\History')->toHtml();
        if ($this->_translator->isAllowed()) {
            $this->_translator->processResponseBody($html);
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Cancel selected orders
     */
    public function massCancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countCancelOrder = 0;
        $countNonCancelOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if ($order->canCancel()) {
                $order->cancel()
                    ->save();
                $countCancelOrder++;
            } else {
                $countNonCancelOrder++;
            }
        }
        if ($countNonCancelOrder) {
            if ($countCancelOrder) {
                $this->_getSession()->addError(__('%1 order(s) cannot be canceled.', $countNonCancelOrder));
            } else {
                $this->_getSession()->addError(__('You cannot cancel the order(s).'));
            }
        }
        if ($countCancelOrder) {
            $this->_getSession()->addSuccess(__('We canceled %1 order(s).', $countCancelOrder));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Hold selected orders
     */
    public function massHoldAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countHoldOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if ($order->canHold()) {
                $order->hold()
                    ->save();
                $countHoldOrder++;
            }
        }

        $countNonHoldOrder = count($orderIds) - $countHoldOrder;

        if ($countNonHoldOrder) {
            if ($countHoldOrder) {
                $this->_getSession()->addError(__('%1 order(s) were not put on hold.', $countNonHoldOrder));
            } else {
                $this->_getSession()->addError(__('No order(s) were put on hold.'));
            }
        }
        if ($countHoldOrder) {
            $this->_getSession()->addSuccess(__('You have put %1 order(s) on hold.', $countHoldOrder));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Unhold selected orders
     */
    public function massUnholdAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUnHoldOrder = 0;
        $countNonUnHoldOrder = 0;

        foreach ($orderIds as $orderId) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if ($order->canUnhold()) {
                $order->unhold()
                    ->save();
                $countUnHoldOrder++;
            } else {
                $countNonUnHoldOrder++;
            }
        }
        if ($countNonUnHoldOrder) {
            if ($countUnHoldOrder) {
                $this->_getSession()->addError(
                    __('%1 order(s) were not released from on hold status.', $countNonUnHoldOrder)
                );
            } else {
                $this->_getSession()->addError(__('No order(s) were released from on hold status.'));
            }
        }
        if ($countUnHoldOrder) {
            $this->_getSession()->addSuccess(
                __('%1 order(s) have been released from on hold status.', $countUnHoldOrder)
            );
        }
        $this->_redirect('*/*/');
    }

    /**
     * Change status for selected orders
     */
    public function massStatusAction()
    {

    }

    /**
     * Print documents for selected orders
     */
    public function massPrintAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $document = $this->getRequest()->getPost('document');
    }

    /**
     * Print invoices for selected orders
     */
    public function pdfinvoicesAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Invoice\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize() > 0) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'invoice' . $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError(
                    __('There are no printable documents related to selected orders.')
                );
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print shipments for selected orders
     */
    public function pdfshipmentsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $shipments = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'packingslip' . $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError(
                    __('There are no printable documents related to selected orders.')
                );
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print credit memos for selected orders
     */
    public function pdfcreditmemosAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $creditmemos = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Creditmemo\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'creditmemo' . $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError(
                    __('There are no printable documents related to selected orders.')
                );
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Print all documents for selected orders
     */
    public function pdfdocsAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                $invoices = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Invoice\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($invoices->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Invoice')->getPdf($invoices);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }

                $shipments = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($shipments->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment')->getPdf($shipments);
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }

                $creditmemos = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Creditmemo\Collection')
                    ->setOrderFilter($orderId)
                    ->load();
                if ($creditmemos->getSize()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($creditmemos);
                    } else {
                        $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($creditmemos);
                        $pdf->pages = array_merge ($pdf->pages, $pages->pages);
                    }
                }
            }
            if ($flag) {
                return $this->_prepareDownloadResponse(
                    'docs' . $this->_objectManager->get('Magento\Core\Model\Date')->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            } else {
                $this->_getSession()->addError(
                    __('There are no printable documents related to selected orders.')
                );
                $this->_redirect('*/*/');
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Attempt to void the order payment
     */
    public function voidPaymentAction()
    {
        if (!$order = $this->_initOrder()) {
            return;
        }
        try {
            $order->getPayment()->void(
                new \Magento\Object() // workaround for backwards compatibility
            );
            $order->save();
            $this->_getSession()->addSuccess(__('The payment has been voided.'));
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We couldn\'t void the payment.'));
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
        }
        $this->_redirect('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'hold':
                $aclResource = 'Magento_Sales::hold';
                break;
            case 'unhold':
                $aclResource = 'Magento_Sales::unhold';
                break;
            case 'email':
                $aclResource = 'Magento_Sales::email';
                break;
            case 'cancel':
                $aclResource = 'Magento_Sales::cancel';
                break;
            case 'view':
                $aclResource = 'Magento_Sales::actions_view';
                break;
            case 'addcomment':
                $aclResource = 'Magento_Sales::comment';
                break;
            case 'creditmemos':
                $aclResource = 'Magento_Sales::creditmemo';
                break;
            case 'reviewpayment':
                $aclResource = 'Magento_Sales::review_payment';
                break;
            case 'address':
            case 'addresssave':
                $aclResource = 'Magento_Sales::actions_edit';
                break;
            default:
                $aclResource = 'Magento_Sales::sales_order';
                break;
        }
        return $this->_authorization->isAllowed($aclResource);
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'orders.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('sales.order.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $this->loadLayout();
        $fileName = 'orders.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock  */
        $exportBlock = $this->getLayout()->getChildBlock('sales.order.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Order transactions grid ajax action
     *
     */
    public function transactionsAction()
    {
        $this->_initOrder();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Edit order address form
     */
    public function addressAction()
    {
        $addressId = $this->getRequest()->getParam('address_id');
        $address = $this->_objectManager->create('Magento\Sales\Model\Order\Address')->load($addressId);
        if ($address->getId()) {
            $this->_coreRegistry->register('order_address', $address);
            $this->loadLayout();
            // Do not display VAT validation button on edit order address form
            $addressFormContainer = $this->getLayout()->getBlock('sales_order_address.form.container');
            if ($addressFormContainer) {
                $addressFormContainer->getChildBlock('form')->setDisplayVatValidationButton(false);
            }

            $this->renderLayout();
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save order address
     */
    public function addressSaveAction()
    {
        $addressId  = $this->getRequest()->getParam('address_id');
        $address    = $this->_objectManager->create('Magento\Sales\Model\Order\Address')->load($addressId);
        $data       = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);
            try {
                $address->save();
                $this->_getSession()->addSuccess(__('You updated the order address.'));
                $this->_redirect('*/*/view', array('order_id' => $address->getParentId()));
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('Something went wrong updating the order address.')
                );
            }
            $this->_redirect('*/*/address', array('address_id' => $address->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }
}
