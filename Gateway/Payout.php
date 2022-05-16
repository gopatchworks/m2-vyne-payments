<?php

namespace Vyne\Magento\Gateway;

class Payout extends ApiAbstract
{
    const ENDPOINT_PAYOUT = 'api/v1/payouts';

    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function paymentRedirect($token, $order_data)
    {
        $request = $this->payoutRequest($token, $order_data);
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
    public function payoutRequest($token, $order_data)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ];

        $httpBody = \GuzzleHttp\json_encode($order_data);

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_PAYOUT),
            $headers,
            $httpBody
        );
    }

}
