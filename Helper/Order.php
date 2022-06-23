<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Helper;

use Vyne\model\Transaction;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class Order extends AbstractHelper
{
    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface 
     */
    protected $orderManagement;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var Data
     */
    protected $vyneHelper;

    /**
     * @var Logger
     */
    protected $vyneLogger;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Data $vyneHelper,
        Logger $vyneLogger,
        CollectionFactory $collectionFactory,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender
    ) {
        parent::__construct($context);
        $this->vyneHelper = $vyneHelper;
        $this->vyneLogger = $vyneLogger;
        $this->collectionFactory = $collectionFactory;
        $this->_transaction = $transaction;
        $this->orderManagement = $orderManagement;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_invoiceService = $invoiceService;
	    $this->creditmemoSender = $creditmemoSender;
        $this->creditmemoLoader = $creditmemoLoader;
    }

    /**
     * update Order History
     *
     * @param \Magento\Sales\Model\Order
     * @param String
     * @param String
     */
    public function updateOrderHistory($order, $msg, $status, $paymentId = null)
    {
        $order->addStatusHistoryComment($msg);
        $order->setState($status)->setStatus($status);

        if ($paymentId) {
            //$this->generatePaidInvoice($order, $paymentId);

            // update order payment
            $payment = $order->getPayment();
            $payment->setData('vyne_transaction_id', $paymentId);
            $payment->setData('last_trans_id', $paymentId);
            $payment->save();
        }

        try {
            $order->save();
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }

    /**
     * capture invoice created for vyne payment
     *
     * @return void
     */
    public function captureVyneInvoice($transaction_id)
    {
        $order = $this->getOrderByVyneTransactionId($transaction_id);
        $this->generatePaidInvoice($order, $transaction_id);
    }

    /**
     * retrieve order invoices
     *
     * @return array
     */
    public function getOrderInvoices($orderId)
    {
        $invoiceData = array();

        if($orderId > 0) {
            $searchCriteria = $this->_searchCriteriaBuilder->addFilter('order_id', $orderId)->create();

            try {
                $invoices = $this->_invoiceRepository->getList($searchCriteria);
                $invoiceData = $invoices->getItems();
            } catch (\Exception $e)  {
                $this->vyneLogger->logException($e);
                $invoiceData = null;
            }
        }

        return $invoiceData;
    }

    /**
     * cancel order by given
     *
     * @param string
     * @return void
     */
    public function cancelOrderById($order_id)
    {
        $this->orderManagement->cancel($order->getId());
    }

    /**
     * change order status by $vyne_transaction_id
     * NOTE: Collection being used because this function need to use Payment model to get Order later
     *
     * @param string
     * @return void
     */
    public function cancelOrderByVyneTransactionId($vyne_transaction_id)
    {
        if ($order = $this->getOrderByVyneTransactionId($vyne_transaction_id)) {
            // cancel order
            $this->orderManagement->cancel($order->getId());
        }
    }

    /**
     * Retrieve magento order using vyne_transaction_id
     *
     * @param string
     * $return Magento\Sales\Model\Order
     */
    public function getOrderByVyneTransactionId($vyne_transaction_id)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('vyne_transaction_id', ['eq' => $vyne_transaction_id]);
        if ($collection->getSize() > 0) {
            $payment = $collection->getFirstItem();
            return $payment->getOrder();
        }

        return null;
    }

    /**
     * generate order invoice for captured vyne transaction
     *
     * @param \Magento\Sales\Model\Order
     * @param string
     *
     * @return void
     */
    public function generatePaidInvoice($order, $vyne_transaction_id)
    {
        try {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            //set Vyne Transaction Id for this invoice
            $invoice->setData('transaction_id', $vyne_transaction_id);
            //set Invoice State to Paid
            $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();

            // set order payment amount paid
            $payment = $order->getPayment();
            $payment->setAmountPaid($invoice->getGrandTotal())->setCanRefund(1);
            $order->setTotalPaid($invoice->getGrandTotal())->setBaseTotalPaid($invoice->getGrandTotal());

            $this->_transaction->addObject($invoice)->addObject($order)->save();
            $this->vyneLogger->logMixed( ['webhook/payment' => 'Invoice #'. $invoice->getId() . ' for Transaction ' . $vyne_transaction_id . ' Captured']);
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }

    /**
     * create credit memo by vyne webhook/refund
     *
     * @param string
     * @param string
     *
     * @return void
     */
    public function createCreditmemo($vyne_transaction_id, $vyne_refund_id)
    {
        $this->vyneLogger->logMixed(['transaction_id' => $vyne_transaction_id, 'refund_id' => $vyne_refund_id]);

        $order = $this->getOrderByVyneTransactionId($vyne_transaction_id);

        $creditMemoData = [];
        $creditMemoData['do_offline'] = 0;
        $creditMemoData['shipping_amount'] = $order->getShippingAmount();
        $creditMemoData['adjustment_positive'] = 0;
        $creditMemoData['adjustment_negative'] = 0;
        $creditMemoData['comment_text'] = __('Credit Memo generated by Vyne');
        $creditMemoData['send_email'] = 1;

        $itemToCredit = [];
        foreach ($order->getAllItems() as $item){
            $itemToCredit[$item->getid(0)] = ['qty'=>1];
        }
        $creditMemoData['items'] = $itemToCredit;

        foreach ($order->getInvoiceCollection() as $invoice) {
            $invoice_id = $invoice->getId();
        }

	    try {
	        $this->creditmemoLoader->setOrderId($order->getId());
	        $this->creditmemoLoader->setCreditmemo($creditMemoData);

	        $creditmemo = $this->creditmemoLoader->load();
	        if ($creditmemo) {
	            if (!$creditmemo->isValidGrandTotal()) {
	                throw new \Magento\Framework\Exception\LocalizedException(
	                    __('The credit memo\'s total must be positive.')
	                );
	            }

	            if (!empty($creditMemoData['comment_text'])) {
	                $creditmemo->addComment(
	                    $creditMemoData['comment_text'],
	                    isset($creditMemoData['comment_customer_notify']),
	                    isset($creditMemoData['is_visible_on_front'])
	                );

	                $creditmemo->setCustomerNote($creditMemoData['comment_text']);
	                $creditmemo->setCustomerNoteNotify(isset($creditMemoData['comment_customer_notify']));
	            }

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	            $creditmemoManagement = $objectManager->create(
	                \Magento\Sales\Api\CreditmemoManagementInterface::class
	            );
	            $creditmemo->getOrder()->setCustomerNoteNotify(!empty($creditMemoData['send_email']));
                $doOffline = isset($data['do_offline']) ? (bool)$data['do_offline'] : false;
                $creditmemoManagement->refund($creditmemo, $doOffline);

	            if (!empty($creditMemoData['send_email'])) {
	                $this->creditmemoSender->send($creditmemo);
	            }
            }
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }
}

