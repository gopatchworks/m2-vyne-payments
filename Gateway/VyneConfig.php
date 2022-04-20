<?php

namespace Vyne\Magento\Gateway;

use Vyne\Magento\Gateway\Configuration as VyneConfiguration; 
use DateTimeImmutable;

class VyneConfig
{
    protected $vyneId;
    protected $accessToken;
    protected $host;
    protected $debug = false;
    protected $environment;

    /**
     * Constructor
     */
    public function __construct($vyneId, $accessToken, $debug=false, $environment="sandbox")
    {
        $this->vyneId = $vyneId;
        $this->accessToken = $accessToken;
        $this->debug = $debug;
        $this->environment = $environment;
        $apiPrefix = $environment === "uat" ? "uat." : "";
        $this->host = "https://" . $apiPrefix . ".app.payvyne.com/api/oauth/token";
    }

    public function setVyneId($vyneId)
    {
        $this->vyneId = $vyneId;
        return $this;
    }

    public function getVyneId()
    {
        return $this->$vyneId;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getPrivateKeyLocation()
    {
        return $this->accessToken;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getConfig()
    {
        $accessToken = $this->getAccessToken();
        $config = VyneConfiguration::getDefaultConfiguration()
            ->setAccessToken($accessToken)
            ->setHost($this->getHost())
            ->setUserAgent("Vyne SDK PHP")
            ->setDebug($this->debug);

        return $config;
    }

    public static function getToken($scopes = array(), $embed = array())
    {
    }
}
