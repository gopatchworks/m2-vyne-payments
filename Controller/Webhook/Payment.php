<?php

declare(strict_types=1);

namespace Vyne\Payments\Controller\Webhook;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Payments\Gateway\Payment as VynePayment;

class Payment extends AbstractWebhookPost
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $requestBody = $this->getRequest()->getContent();
        $this->vyneLogger->logMixed( ['webhook/payment' => $requestBody] );

        $request = json_decode($requestBody);
        // validate jwt json decode
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->vyneLogger->logMixed(
                ['request' => $requestBody],
                __('A Webhook request was received with a malformed body. Error: %1', json_last_error_msg())
            );

            $result->setHttpResponseCode(400);
            return $result;
        }

        try {
            $this->vyneLogger->logMixed(['increment_id' => $request->merchantReference]);
            $order = $this->getOrderByIncrementId($request->merchantReference);
            $this->vyneLogger->logMixed(['webhook/payment/order' => $order->getData()]);
            $vyne_status = $request->status;
            $order_status = VynePayment::getTransactionAction($vyne_status);
            $this->vyneLogger->logMixed( ['webhook/payment' => $order_status] );

            switch ($order_status) {
            case VynePayment::GROUP_PROCESSING:
            case VynePayment::GROUP_PENDING_PAYMENT:
                $this->vyneOrder->updateOrderHistory($order, __('Order Status Updated by Vyne'), $order_status, null, $vyne_status);

                break;
            case VynePayment::GROUP_SUCCESS:
            case VynePayment::GROUP_RECEIVED_PAYMENT:
                $this->vyneOrder->updateOrderHistory($order, __('Transaction Completed by Vyne'), $order_status, $request->paymentId, $vyne_status);

                break;
            case VynePayment::GROUP_CANCEL:
                // logic to send transaction failed email in the vyneOrder helper
                $this->vyneOrder->cancelOrder($order);
                break;
            case VynePayment::GROUP_REFUND:
                $this->vyneOrder->updateOrderTotalRefund($order, $request->refundedAmount);
                break;
            }

        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $result->setHttpResponseCode(200);

        return $result;
    }

    /**
     * retrieve Magento order using increment id (merchantReference) using search criteriaBuilder
     *
     * @param string $incrementId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrderByIncrementId($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orderList = $this->orderRepository->getList($searchCriteria)->getItems();
        if (count($orderList) === 0) {
            throw new \Exception(__('Order not found'));
        }

        return array_shift($orderList);
    }
}
