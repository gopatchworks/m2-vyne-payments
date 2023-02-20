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
     * @param Configuration $config
     * @param String $environment
     * @param ClientInterface $client
     */
    public function __construct(
        Configuration $config = null,
        ClientInterface $client = null
    ) {
        $this->config = $config ?: new Configuration();
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

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $content = $response->getBody()->getContents();

            return json_decode($content);

        }
        catch (\Exception $e) {
            $response_e = $e->getResponse();
            $statusCode_e = $response_e->getStatusCode();

            switch($statusCode_e) {
            case 400:
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode_e,
                        (string) $request->getUri()
                    ),
                    $statusCode_e,
                    $response_e->getHeaders(),
                    (string) $response_e->getBody()
                );
            case 401:
                // handle 401
                return $response_e->getBody();
            case 404:
                // handle 404
                return $response_e->getBody();
            default:
                return [
                    $content,
                    $response_e->getStatusCode(),
                    $response_e->getHeaders()
                ];
            }
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
            $this->getConfig()->setApiKey('Authorization', $response->access_token)->setApiKeyPrefix('Authorization', 'Bearer');
            $this->getConfig()->setApiKey('Content-Type', 'application/json');
        }
        catch (\Exception $e) {
            // handle exception
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
    public function tokenRequest()
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $httpBody = \GuzzleHttp\Psr7\build_query($this->getConfig()->getClientCredential());

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_TOKEN),
            $headers,
            $httpBody
        );
    }
}
