<?php

namespace Vyne\Magento\Gateway;

class Configuration
{
    /**
     * environment constants
     */
    const ENV_STAGING = "staging";
    const ENV_PRODUCTION = "production";

    /**
     * The host urls
     *
     * @var string
     */
    protected $staging_host = 'https://uat.app.payvyne.com';
    protected $production_host = 'https://app.payvyne.com';

    /**
     * @var Configuration
     */
    private static $defaultConfiguration;

    /**
     * Associate array to store client credential
     * @var string[]
     */
    protected $credential = [
        'grant_type' => 'client_credentials',
        'client_id' => '',
        'client_secret' => ''
    ];

    /**
     * Associate array to store API keys
     *
     * @var string[]
     */
    protected $apiKeys = [];

    /**
     * Associate array to store API prefix (for example : Bearer)
     *
     * @var string[]
     */
    protected $apiKeyPrefixes = [];

    /**
     * Access token for OAuth/Bearer authentication
     *
     * @var string
     */
    protected $paymentToken = '';

    /**
     * environment
     *
     * @var string
     */
    protected $environment = 'production';

    /**
     * Debug switch 
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Debug file location
     *
     * @var string
     */
    protected $debugFile = 'php://output';

    /**
     * Temporary folder
     *
     * @var string
     */
    protected $tempFolderPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tempFolderPath = sys_get_temp_dir();
    }

    /**
     * set client credentials
     *
     * @return $this
     */
    public function setClientCredentials($client_id, $client_secret)
    {
        $this->credential['client_id'] = $client_id;
        $this->credential['client_secret'] = $client_secret;
        return $this;
    }

    /**
     * retrieve credential array
     *
     * @return string[]
     */
    public function getClientCredentials()
    {
        return $this->credential;
    }

    /**
     * Sets API key
     *
     * @param string $apiKeyIdentifier
     * @param string $key
     *
     * @return $this
     */
    public function setApiKey($apiKeyIdentifier, $key)
    {
        $this->apiKeys[$apiKeyIdentifier] = $key;
        return $this;
    }

    /**
     * Gets API key
     *
     * @param string $apiKeyIdentifier
     *
     * @return null|string API key or token
     */
    public function getApiKey($apiKeyIdentifier)
    {
        return isset($this->apiKeys[$apiKeyIdentifier]) ? $this->apiKeys[$apiKeyIdentifier] : null;
    }

    /**
     * Sets the prefix for API key (e.g. Bearer)
     *
     * @param string $apiKeyIdentifier
     * @param string $prefix
     *
     * @return $this
     */
    public function setApiKeyPrefix($apiKeyIdentifier, $prefix)
    {
        $this->apiKeyPrefixes[$apiKeyIdentifier] = $prefix;
        return $this;
    }

    /**
     * Gets API key prefix
     *
     * @param string $apiKeyIdentifier
     *
     * @return null|string
     */
    public function getApiKeyPrefix($apiKeyIdentifier)
    {
        return isset($this->apiKeyPrefixes[$apiKeyIdentifier]) ? $this->apiKeyPrefixes[$apiKeyIdentifier] : null;
    }

    /**
     * Sets the access token for OAuth
     *
     * @param string $paymentToken Token for OAuth
     *
     * @return $this
     */
    public function setPaymentToken($paymentToken)
    {
        $this->paymentToken = $paymentToken;
        return $this;
    }

    /**
     * Gets the access token for OAuth
     *
     * @return string Access token for OAuth
     */
    public function getPaymentToken()
    {
        return $this->paymentToken;
    }

    /**
     * Gets the host
     *
     * @return string Host
     */
    public function getHost()
    {
        if ($this->environment == self::ENV_STAGING) {
            return $this->staging_host;
        }

        return $this->production_host;
    }

    /**
     * Sets debug flag
     *
     * @param bool $debug Debug flag
     *
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Gets the debug flag
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Sets environment flag
     *
     * @param string
     *
     * @return $this
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * Gets the environment flag
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Sets the debug file
     *
     * @param string $debugFile Debug file
     *
     * @return $this
     */
    public function setDebugFile($debugFile)
    {
        $this->debugFile = $debugFile;
        return $this;
    }

    /**
     * Gets the debug file
     *
     * @return string
     */
    public function getDebugFile()
    {
        return $this->debugFile;
    }

    /**
     * Sets the temp folder path
     *
     * @param string $tempFolderPath Temp folder path
     *
     * @return $this
     */
    public function setTempFolderPath($tempFolderPath)
    {
        $this->tempFolderPath = $tempFolderPath;
        return $this;
    }

    /**
     * Gets the temp folder path
     *
     * @return string Temp folder path
     */
    public function getTempFolderPath()
    {
        return $this->tempFolderPath;
    }

    /**
     * Gets the default configuration instance
     *
     * @return Configuration
     */
    public static function getDefaultConfiguration()
    {
        if (self::$defaultConfiguration === null) {
            self::$defaultConfiguration = new Configuration();
        }

        return self::$defaultConfiguration;
    }

    /**
     * Sets the detault configuration instance
     *
     * @param Configuration
     *
     * @return void
     */
    public static function setDefaultConfiguration(Configuration $config)
    {
        self::$defaultConfiguration = $config;
    }

    /**
     * Get API key - with prefix is configured
     *
     * @param  string $apiKeyIdentifier
     *
     * @return null|string API key with the prefix
     */
    public function getApiKeyWithPrefix($apiKeyIdentifier)
    {
        $prefix = $this->getApiKeyPrefix($apiKeyIdentifier);
        $apiKey = $this->getApiKey($apiKeyIdentifier);

        if ($apiKey === null) {
            return null;
        }

        if ($prefix === null) {
            $keyWithPrefix = $apiKey;
        } else {
            $keyWithPrefix = $prefix . ' ' . $apiKey;
        }

        return $keyWithPrefix;
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOptions()
    {
        $options = [];
        // client options logic

        return $options;
    }
}
