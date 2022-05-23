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
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Data $vyneHelper,
        Logger $vyneLogger,
        CollectionFactory $collectionFactory,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService
    ) {
        parent::__construct($context);
        $this->vyneHelper = $vyneHelper;
        $this->vyneLogger = $vyneLogger;
        $this->collectionFactory = $collectionFactory;
        $this->_transaction = $transaction;
        $this->orderManagement = $orderManagement;
        $this->_invoiceService = $invoiceService;
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
            $this->generatePaidInvoice($order, $paymentId);

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
     */
    public function generatePaidInvoice($order, $vyne_transaction_id)
    {
        try {
            $invoice = $this->_invoiceService->prepareInvoice($order);
            //set Vyne Transaction Id for this invoice
            $invoice->setData('transaction_id', $vyne_transaction_id);
            //set Invoice State to Paid
            //$invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
            $invoice->register();
            $this->_transaction->addObject($invoice)->addObject($order)->save();
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }
    }
}

