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

class ApiAbstract
{
    const ENDPOINT_TOKEN = 'api/oauth/token';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var String
     */
    protected $environment;

    /**
     * @param Configuration $config
     * @param String $environment
     * @param ClientInterface $client
     */
    public function __construct(
        Configuration $config = null,
        $environment = null,
        ClientInterface $client = null
    ) {
        $this->config = $config ?: new Configuration();
        $this->environment = $environment;
        $this->client = $client ?: new Client();
    }

    /**
     * get Configuration class
     *
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * retrieve payment url base on environment
     *
     * @return string
     */
    public function getEndpointUrl($endpoint)
    {
        return $this->getConfig()->getHost() . '/' . $endpoint;
    }

    /**
     * send request to Vyne and handle response & exceptions
     *
     * @return stdClass | ApiException | boolean 
     */
    public function sendRequest($request, $options)
    {
        try {
            $response = $this->client->send($request, $options);
        }
        catch (\Exception $e) {
            // log undefined errors here
        }

        return false;
    }

    /**
     * initialize payment token and assign it to the Configuration object
     *
     * @return this
     */
    public function initToken()
    {
        $request = $this->tokenRequest();
        $options = $this->getConfig()->createHttpClientOptions();

        try {
            $response = $this->sendRequest($request, $options);
        }
        catch (\Exception $e) {
        }

        return $this;
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

        $httpBody = \GuzzleHttp\Psr7\build_query($this->getConfig()->getCredential());

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_TOKEN),
            $headers,
            $httpBody
        );
    }
}
