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
            $order = $this->orderRepository->get($request->merchantReference);
            $vyne_status = $request->status;
            $order_status = VynePayment::getTransactionAction($vyne_status);
            $this->vyneLogger->logMixed( ['webhook/payment' => $order_status] );

            switch ($order_status) {
            case VynePayment::GROUP_PROCESSING:
            case VynePayment::GROUP_PENDING_PAYMENT:
                $this->vyneOrder->updateOrderHistory($order, __('Order Status Updated by Vyne'), $order_status, null, $vyne_status);

                break;
            case VynePayment::GROUP_SUCCESS:
                $this->vyneOrder->updateOrderHistory($order, __('Order Completed by Vyne'), $order_status, $request->paymentId, $vyne_status);

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
}
