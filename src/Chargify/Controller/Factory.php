<?php

namespace Chargify\Controller;

use Guzzle\Http\Client;
use Guzzle\Plugin\CurlAuth\CurlAuthPlugin;
use Guzzle\Log\Zf1LogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\MessageFormatter;
use Exception;

class Factory
{
    private $domain = null;
    private $apiKey = null;
    public $debugLocation = '';

    /**
     * If called as instance set some basic variables for later use.
     */
    public function __construct( $domain, $apiKey, $debugLocation=null )
    {
        $this->domain = $domain;
        $this->apiKey = $apiKey;
        $this->debugLocation = $debugLocation;
    }

    /**
     * Simulates an instance call but converts the instance method into the name
     * parameter of a static build call 
     * 
     * eg: $factory->product() becomes Factory::Build('product', [])
     * 
     * @param string $name name of method
     * @param array $arguments
     * @return Chargify\Controller
     */
    public function __call( $name, $arguments )
    {
        return self::build( $name, $this->domain, $this->apiKey, $this->debugLocation );
    }

    public static function build( $type, $domain, $apiKey, $debugLocation=null ) 
    {
        // Get the base url for all the connections.
        $base_url = sprintf('https://%s.chargify.com', $domain);

        // Set default headers to be sent on each request
        $configOptions = [ 
            'request.options' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json' 
                ],
                'exceptions' => false,
            ]
        ];
        
        $client = new Client( $base_url, $configOptions );
        $client->addSubscriber( new CurlAuthPlugin($apiKey, 'x') );

        // If a debug location has been specified then add it to the options 
        // when we instanciate our client.
        if ( $debugLocation )
        {
            $fp = fopen( $debugLocation, 'a' );
            $client->addSubscriber( LogPlugin::getDebugPlugin(true, $fp) );
        }

        $className = 'Chargify\\Controller\\' . ucfirst($type);

        if ( class_exists($className) ) {
            return new $className($client);
        }
        else {
            throw new Exception("Invalid controller type given.");
        }
    }
}