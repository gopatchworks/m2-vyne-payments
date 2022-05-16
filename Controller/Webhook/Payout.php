<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

use Magento\Framework\Controller\ResultFactory;

class Payout extends AbstractWebhookGet
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $payload = $this->getRequest()->getParam('payvyne_payment_payload');
        $order_id = $this->checkoutSession->getLastOrderId();
        $order = $this->orderRepository->get($order_id);

        $body = $this->vyneHelper->decodeJWTBase64($payload);
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
            $order_status = $this->vyneHelper->getOrderStatus($body->paymentStatus);
            $this->vyneOrder->updateOrderHistory($order, __('Order Updated by Vyne'), $order_status, $body->paymentId);
            return $this->resultRedirect->setPath('checkout/onepage/success', array('_secure'=>true));
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $this->messageManager->addNoticeMessage(__('Vyne Payment Webhook failed. Please contact us for support'));
        return $this->resultRedirect->setUrl('/');
    }
}
