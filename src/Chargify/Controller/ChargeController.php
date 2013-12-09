<?php

namespace Chargify\Controller;

use \Chargify\Resource\ChargeResource as Resource;

class Charge extends AbstractController 
{
    /**
     *
     */
    public function create( $subscriptionId, $data )
    {
        $response = $this->request('subscriptions/' . $subscriptionId . '/charges' , $data, 'POST');
        $charge = new Resource( $response['charge'] );

        return $charge;
    }
}