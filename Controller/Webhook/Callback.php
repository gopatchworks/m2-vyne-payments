<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Magento\Gateway\Payment as VynePayment;

class Callback extends AbstractWebhookGet
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
            switch (VynePayment::getTransactionAction($body->paymentStatus)) {
            case VynePayment::GROUP_PROCESSING:
                $order_status = VynePayment::GROUP_PROCESSING;
                $this->vyneOrder->updateOrderHistory($order, __('Order Updated by Vyne'), $order_status, $body->paymentId);
                return $this->resultRedirect->setPath('checkout/onepage/success', array('_secure'=>true));

                break;
            case VynePayment::GROUP_CANCEL:
                return $this->failedVynePayment($order_id);

                break;
            case VynePayment::GROUP_SUCCESS:
                break;
            case VynePayment::GROUP_REFUND:
                break;
            }

        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $this->messageManager->addNoticeMessage(__('Vyne Payment Webhook failed. Please contact us for support'));
        return $this->resultRedirect->setUrl('/');
    }

    /**
     * reinit shopping cart if payment is failed
     *
     * @return ResultInterface
     */
    public function failedVynePayment($order_id)
    {
        $this->messageManager->addNoticeMessage(__('Vyne payment failed. Please contact us for support'));
        $this->vyneCart->reinitCart($order_id);

        return $this->resultRedirect->setUrl('/checkout/cart/');
    }
}
