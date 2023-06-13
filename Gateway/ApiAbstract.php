<?php

namespace Vyne\Payments\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Vyne\Payments\Gateway\ApiException;
use Vyne\Payments\Gateway\Configuration;
use Vyne\Payments\Model\TokenRepository;
use Vyne\Payments\Model\TokenFactory;

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
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

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

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->tokenRepository = $objectManager->create('Vyne\Payments\Model\TokenRepository');
        $this->tokenFactory = $objectManager->create('Vyne\Payments\Model\TokenFactory');
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
     * set token for the next request
     *
     * @return this
     */
    public function setToken()
    {
        $token_model = $this->tokenRepository->getValidToken();

        if (!$token_model) {
            if ($vyne_token = $this->initToken()) {
                $token_model = $this->tokenFactory->create();
                $token_model->setAccessToken($vyne_token->access_token);
                $token_model->setMerchantId($vyne_token->merchantId);
                $token_model->setScope($vyne_token->scope);
                $token_model->setIss($vyne_token->iss);
                $token_model->setMerchant($vyne_token->merchant);
                $token_model->setMfaRequired($vyne_token->mfa_required);
                $token_model->setTokenType($vyne_token->token_type);
                $token_model->setExpireIn($vyne_token->expires_in);
                $token_model->setCreatedAt(date('Y-m-d H:i:s'));

                $this->tokenRepository->save($token_model);
            }
        }

        $this->getConfig()->setApiKey('Authorization', $token_model->getAccessToken())->setApiKeyPrefix('Authorization', 'Bearer');
        $this->getConfig()->setApiKey('Content-Type', 'application/json');

        return $this;
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
            return $response;
        }
        catch (\Exception $e) {
            // handle exception
            return false;
        }
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

        $httpBody = \GuzzleHttp\Psr7\Query::build($this->getConfig()->getClientCredential());

        return new Request(
            'POST',
            $this->getEndpointUrl(self::ENDPOINT_TOKEN),
            $headers,
            $httpBody
        );
    }
}
