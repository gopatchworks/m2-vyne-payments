<?php

declare(strict_types=1);

namespace Vyne\Payments\Controller\Webhook;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Payments\Gateway\Refund as VyneRefund;

class Refund extends AbstractWebhookPost
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $requestBody = $this->getRequest()->getContent();
        $this->vyneLogger->logMixed( ['webhook/refund/request' => $requestBody] );
        $request = json_decode($requestBody);

        // validate request body
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->vyneLogger->logMixed(
                ['request' => $requestBody],
                __('A Webhook request was received with a malformed body. Error: %1', json_last_error_msg())
            );

            $result->setHttpResponseCode(400);
            return $result;
        }

        try {
            // placeholder to handle webhook request
            $this->vyneLogger->logMixed( ['webhook/refund' => $request->status] );

            $order = $this->vyneOrder->getOrderByVyneTransactionId($request->paymentId);
            $payment = $order->getPayment();
            $this->vyneOrder->updateRefundByPayment($order, $payment, $request->refundId, $request->amount, $request->status);

            if ($request->status == VyneRefund::GROUP_COMPLETED
                && $request->amount) {
                $this->vyneOrder->createCreditmemo($order, $request->refundId, $request->amount);
            }

            // save changes to order
            $order->save();

        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $result->setHttpResponseCode(200);

        return $result;
    }
}
