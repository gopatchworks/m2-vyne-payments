<?php

namespace Vyne\Magento\Gateway;

use \Exception;

class ApiException extends Exception
{

    /**
     * @var \stdClass | string | null
     */
    protected $responseBody;

    /**
     * @var string[] | null
     */
    protected $responseHeaders;

    /**
     * @var \stdClass | string | null
     */
    protected $responseObject;

    /**
     * Constructor
     *
     * @param string $message
     * @param int $code
     * @param string[]|null $responseHeaders
     * @param \stdClass|string|null $responseBody
     */
    public function __construct($message = "", $code = 0, $responseHeaders = [], $responseBody = null)
    {
        parent::__construct($message, $code);
        $this->responseHeaders = $responseHeaders;
        $this->responseBody = $responseBody;
    }

    /**
     * Gets the HTTP response header
     *
     * @return string[] | null
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Gets the HTTP body of the server response as Json
     *
     * @return \stdClass | string | null
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Set the deseralized response object
     *
     * @param mixed $obj Deserialized response object
     *
     * @return void
     */
    public function setResponseObject($obj)
    {
        $this->responseObject = $obj;
    }

    /**
     * Get the deseralized response object
     *
     * @return mixed the deserialized response object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}
