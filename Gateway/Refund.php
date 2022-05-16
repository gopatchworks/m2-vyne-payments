<?php

namespace Vyne\Magento\Gateway;

class Refund extends ApiAbstract
{
    const ENDPOINT_REFUND = 'api/v1/refunds/init';

    const STATUS_NEW = 'The refund is submitted.';
    const STATUS_PROCESSING = 'The refund is processing and the transfer is in progress.';
    const STATUS_COMPLETED = 'The refund is successful and the transfer was completed.';
    const STATUS_FALIED = 'Processing the refund or transferring the money to the consumer failed.';

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function paymentRefund($token, $order_data)
    {
        $request = $this->refundRequest($token, $order_data);
        $options = $this->createHttpClientOptions();

        $response = $this->client->send($request, $options);
        $redirect_content = $response->getBody()->getContents();
        $redirect_obj = json_decode($redirect_content);

        return $redirect_obj->redirectUrl;
    }

    /**
     * prepare request body for payment refund request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function refundRequest($token, $order_data)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $httpBody = \GuzzleHttp\json_encode($order_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_REFUND),
            $headers,
            $httpBody
        );
    }

}
