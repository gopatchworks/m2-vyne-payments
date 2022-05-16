<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

class Payment extends AbstractWebhook
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\ResultInterface $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $payload = $this->getRequest()->getParam('payvyne_payment_payload');
        $last_real_order_id = $this->checkoutSession->getLastRealOrderId();

        $body = $this->vyneHelper->decodeJWTBase64($payload);
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
            $this->orderHelper->updateOrderHistory($order, __('Order Updated by Vyne'), $body['paymentStatus'], $body['paymentId']);
            return $this->resultRedirect->setPath('checkout/onepage/success', array('_secure'=>true));
        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $this->messageManager->addNoticeMessage(__('Vyne Payment Webhook failed. Please contact us for support'));
        return $this->resultRedirect->setUrl('/');
    }
}
