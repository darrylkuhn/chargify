<?php

namespace Chargify\Exception;

/**
 * Chargify Response exception
 */
class ResponseException extends \RuntimeException
{
    /**
     * HTTP response code
     * @var int
     */
    protected $responseCode = null;

    /**
     * @var array
     */
    protected $responseJson = array();

    /**
     * @var array
     */
    protected $isJson = false;

    /**
     * Construct Exception
     *
     * @param string $responseBody HTTP response body
     * @param int $responseCode HTTP response code
     */
    public function __construct( $responseBody, $responseCode )
    {
        $this->responseCode = $responseCode;

        if ( $jsonData = json_decode($responseBody) )
        {
            $this->responseJson = $jsonData;
            $this->isJson = true;
        }

        parent::__construct( $responseBody );
    }

    /**
     * Returns bool to indicate response content type
     *
     * @return bool
     */
    public function isJson()
    {
        return $this->isJson;
    }

    /**
     * Returns parsed json data response
     *
     * @return array
     */
    public function json()
    {
        return $this->responseJson;
    }

    /**
     * Returns HTTP response code from request
     *
     * @return int
     */
    public function responseCode()
    {
        return $this->responseCode;
    }
}
