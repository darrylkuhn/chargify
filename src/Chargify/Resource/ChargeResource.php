<?php

namespace Chargify\Resource;

class ChargeResource extends AbstractResource 
{
    public $id;
    public $success;
    public $memo;
    public $amount_in_cents;
    public $ending_balance_in_cents;
    public $type;
    public $transaction_type;
    public $subscription_id;
    public $product_id;
    public $created_at;
    public $payment_id;

    public function getName() {
        return 'charge';
    }

    public function getFilter() {
        return array(
            'created_at' => function($value) { return new \DateTime($value); },
        );
    }
}