<?php

namespace Vyne\Magento\Gateway;

class Payment extends ApiAbstract
{
    const ENDPOINT_PAYMENT = 'api/v1/payments';

    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSING_FAILED = 'processing_failed';
    const STATUS_CAPTURE_SUCCEEDED = 'capture_succeeded';
    const STATUS_CAPTURE_PENDING = 'capture_pending';
    const STATUS_CAPTURE_DECLINED = 'capture_declined';
    const STATUS_CAPTURE_FAILED = 'capture_failed';
    const STATUS_AUTHORIZATION_SUCCEEDED = 'authorization_succeeded';
    const STATUS_AUTHORIZATION_PENDING = 'authorization_pending';
    const STATUS_AUTHORIZATION_DECLINED = 'authorization_declined';
    const STATUS_AUTHORIZATION_FAILED = 'authorization_failed';
    const STATUS_AUTHORIZATION_EXPIRED = 'authorization_expired';
    const STATUS_AUTHORIZATION_VOIDED = 'authorization_voided';

    const PAYMENT_SOURCE_ECOMMERCE = 'ecommerce';
    const PAYMENT_SOURCE_RECURRING = 'recurring';

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function paymentRedirect($token, $order_data)
    {
        $request = $this->redirectRequest($token, $order_data);
        $options = $this->createHttpClientOptions();

        $response = $this->client->send($request, $options);
        $redirect_content = $response->getBody()->getContents();
        $redirect_obj = json_decode($redirect_content);

        return $redirect_obj->redirectUrl;
    }

    /**
     * prepare request body for payment redirect request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function redirectRequest($token, $order_data)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $httpBody = \GuzzleHttp\json_encode($order_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_PAYMENT),
            $headers,
            $httpBody
        );
    }

}
