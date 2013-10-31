<?php

namespace Chargify\Controller;

use \Chargify\Exception\ResponseException;
use \Exception;
use \RuntimeException;

abstract class AbstractController 
{
    /**
     * @var \Guzzle\Http\ClientInterface
     */
    protected $client;

    /**
     * Build a Controller with the supplied Guzzle Client
     */
    public function __construct( \Guzzle\Http\ClientInterface $client )
    {
        $this->client = $client;
    }

    /**
     * Issue a request to the specified URI using the HTTP verb specified by 
     * $method including body content (if given).
     * 
     * @param string $uri 
     * @param array $body payload ot include in request. will be converted to 
     *        JSON
     * @param string $method HTTP verb to use during call
     * @return array
     * @throws Exception, \Chargify\Exception\ResponseException
     */
    protected function request($uri, array $body = array(), $method = 'GET') 
    {
        $data = null;

        $method = strtolower($method);

        // Build basic request using the method and URI supplied
        $request = $this->client->$method( $uri );
        
        // If a body has been specified add it to the request... in theory a 
        // caller could specify body content on a GET request but we'll expect 
        // them to be smarter than that or let our HTTP client throw an 
        // exception if not :)
        if ( count($body) > 0 )
        {
            $request->setBody( json_encode($body) );
        }

        $response = $request->send();

        // Try to parse the response... it's possible (though very unlikely)
        // that chargify could return a non-json response in which case we
        // simply catch and rethrow the error up.
        try 
        {
            $data = $response->json();
        }
        catch ( RuntimeException $e )
        {
            throw new Exception( 'Response from Chargify server was not valid JSON' );
        }
        
        if ( ! $response->isSuccessful() ) 
        {
            throw new ResponseException( $response->getBody(), $response->getStatusCode() );
        }

        return $data;
    }

    /**
     * Return the HTTP client set on this instance 
     * 
     * @return \Guzzle\Http\ClientInterface
     */
    public function getClient() 
    {
        return $this->client;
    }

}