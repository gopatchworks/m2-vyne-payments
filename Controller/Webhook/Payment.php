<?php

declare(strict_types=1);

namespace Vyne\Magento\Controller\Webhook;

use Magento\Framework\Controller\ResultFactory;
use Vyne\Magento\Gateway\Payment as VynePayment;

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
            $this->vyneLogger->logMixed( ['webhook/payment' => VynePayment::getTransactionAction($request->status)] );
            switch (VynePayment::getTransactionAction($request->status)) {
            case VynePayment::GROUP_PROCESSING:
            case VynePayment::GROUP_SUCCESS:
                $this->vyneOrder->captureVyneInvoice($request->paymentId);

                break;
            case VynePayment::GROUP_CANCEL:
                $this->vyneOrder->cancelOrderById($order->getId());
                break;
            }
            // placeholder to handle webhook request

        }
        catch (\Exception $e) {
            $this->vyneLogger->logException($e);
        }

        $result->setHttpResponseCode(200);

        return $result;
    }
}
