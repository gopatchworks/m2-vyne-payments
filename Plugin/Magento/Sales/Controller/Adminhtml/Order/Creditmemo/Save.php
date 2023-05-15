<?php
/**
 * Copyright ©  All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Vyne\Magento\Plugin\Magento\Sales\Controller\Adminhtml\Order\Creditmemo;

use Vyne\Magento\Helper\Data as VyneHelper;

class Save
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var VyneHelper
     */
    protected $vyneHelper;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param VyneHelper $vyneHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        VyneHelper $vyneHelper
    ) {
        $this->_request = $request;
        $this->resultFactory = $resultFactory;
        $this->orderRepository = $orderRepository;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->messageManager = $messageManager;
        $this->vyneHelper = $vyneHelper;
    }

    /**
     * whenever cart is saved, interact with vyne
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return void
     */
    public function aroundExecute(
        \Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save $subject,
        \Closure $proceed
    ) {
        $order_id = $this->_request->getParam('order_id');
        $order = $this->orderRepository->get($order_id);
        $data = $this->_request->getPost('creditmemo');

        // if payment method is Vyne, let Vyne server handle credit memo creation via /vyne/webhook/refund
        if ($order->getPayment()->getMethod() == \Vyne\Magento\Model\Payment\Vyne::PAYMENT_METHOD_CODE) {
            $this->creditmemoLoader->setOrderId($this->_request->getParam('order_id'));
            $this->creditmemoLoader->setCreditmemoId($this->_request->getParam('creditmemo_id'));
            $this->creditmemoLoader->setCreditmemo($this->_request->getParam('creditmemo'));
            $this->creditmemoLoader->setInvoiceId($this->_request->getParam('invoice_id'));
            $creditmemo = $this->creditmemoLoader->load();

            try {
                $amount = number_format(floatval($creditmemo->getGrandTotal()), 2, '.', '');
                $this->refund($order->getPayment(), $amount);
                $msg = __('You have requested Refund Request. Vyne is processing it.');

                $this->messageManager->addSuccessMessage($msg);
                // append msg to order comment section
                $order->addStatusHistoryComment($msg);
                $order->save();
            }
            catch (\Exception $e) {
                $msg = __($e->getMessage());
                $this->messageManager->addErrorMessage($msg);
            }

            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('sales/order/view', ['order_id' => $order_id]);
            return $resultRedirect;
        }

        // default behavior
        return $proceed();
    }

    /**
     * private refund function for custom logic . reference in \Vyne\Magento\Model\Payment\Vyne
     * send refund request to Vyne only
     *
     * @return void
     */
    private function refund($payment, $amount)
    {
        $refundApi = $this->vyneHelper->initRefund();
        $transaction_id = $payment->getData('vyne_transaction_id');

        // send refund request and retrieve response
        $response = $refundApi->paymentRefund($transaction_id, $amount);

        if (is_array($response->errors) && count($response->errors) > 0) {
            $errors = [];
            foreach ($response->errors as $error){
                if ($error->errorMessage == 'The refund amount cannot be greater than the payment amount') {
                    $errors[] = 'Error: Insufficient balance in your Vyne Settlement Account. Please top up your Settlement Account or wait for funds to become available before trying again';
                }
                elseif ($error->errorMessage == 'Insufficient funds for refund') {
                    // do nothing so we have empty errors array
                    //$errors[] = 'Insufficient funds for refund';
                }
                else {
                    $errors[] = "Issue with Payment #{$error->paymentId} : {$error->errorMessage}";
                }
            }

            if (count($errors) > 0) {
                throw new \Exception(implode(',' , $errors));
            }
        }
    }
}
