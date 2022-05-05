<?php

namespace Vyne\Magento\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Vyne\Magento\Gateway\ApiException;
use Vyne\Magento\Gateway\Configuration;

class Payment
{
    const TOKEN_URL = 'https://uat.app.payvyne.com/api/oauth/token';
    const PAYMENT_URL = 'https://uat.app.payvyne.com/api/v1/payments';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param int             $hostIndex (Optional)
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->hostIndex = $hostIndex;
    }

    /**
     * request vyne payment Token
     *
     * @param array
     */
    public function requestToken($client_id, $client_secret)
    {
        $request = $this->tokenRequest($client_id, $client_secret);
        $options = $this->createHttpClientOption();

        return $this->client->send($request, $options);
    }


    /**
     * retrieve payment redirect using given order data
     *
     * @param array
     * @return string
     */
    public function requestPaymentRedirect($token, $order_data)
    {
        $request = $this->redirectRequest($token, $order_data);
        $options = $this->createHttpClientOption();

        return $this->client->send($request, $options);
    }

    /**
     * prepare request body for token request
     *
     * @param  array
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function tokenRequest($client_id, $client_secret)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $formParams = [
            'grant_type' => 'client_credentials',
            'client_id' => $client_id, 
            'client_secret' => $client_secret
        ];
        $httpBody = \GuzzleHttp\Psr7\build_query($formParams);

        return new Request(
            'POST',
            self::TOKEN_URL,
            $headers,
            $httpBody
        );
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
            self::PAYMENT_URL,
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        // client options logic

        return $options;
    }
}
