<?php

namespace Chargify\Controller;

use \Chargify\Resource\SubscriptionResource as Resource;
use \Chargify\Resource\ChargeResource as Adjustment;

class Subscription extends AbstractController {

    /**
     * Get all subscriptions.
     *
     * @return  subscription objects
     */
    public function getAll() {
        $subscriptions = array();
        // Get the raw data from Chargify.
        $response = $this->request('subscriptions');

        // Convert the raw data into resource objects.
        foreach ($response as $data ) {
            if (is_array($data) && is_array($data['subscription'])) {
                $subscriptions[] = new Resource($data['subscription']);
            }
        }

        return $subscriptions;
    }

    /**
     * Read a subscription by id.
     *
     * @param $id The Chargify subscription ID.
     * @return A chargify subscription object.
     */
    public function getById($id) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id);

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Get all subscriptions for a specific customer.
     *
     * @param  $id customer id.
     * @return  subscription objects
     */
    public function getByCustomer($id) {
        $subscriptions = array();

        $response = $this->request('customers/' . $id . '/subscriptions');

        // Convert the raw data into resource objects.
        foreach ($response as $data ) {
            if (is_array($data) && is_array($data['subscription'])) {
                $subscriptions[] = new Resource($data['subscription']);
            }
        }

        return $subscriptions;
    }

    /**
     * Create a new subscription.
     *
     * @param $data Keyed array of data according to API docs.
     * @return \Chargify\Resource\SubscriptionResource
     */
    public function create( $data ) 
    {
        $response = $this->request( 'subscriptions', $data, 'POST' );
        $subscription = new Resource( $response['subscription'] );
        return $subscription;
    }

    /**
     * Update an existing subscription.
     *
     * @param $id The Chargify subscription ID.
     * @param  $data Keyed array of data according to API docs.
     * @return  Updated chargify object.
     */
    public function update($id, $data) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id, $data, 'PUT');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Cancel a subscription at the end of the subscription period.
     *
     * @param $id The Chargify subscription ID.
     * @return  Cancelled chargify object.
     */
    public function cancel($id) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id, array(), 'DELETE');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Cancel a subscription at the end of the cycle.
     *
     * @param $id The Chargify subscription ID.
     */
    public function cancelDelayed( $id, $message=null ) 
    {
        $subscription = null;

        $data = ['subscription' => 
            [ 'cancel_at_end_of_period' => 1,
              'cancellation_message' => $message ]
        ];

        $response = $this->request('subscriptions/' . $id, $data, 'PUT');
        $subscription = new Resource($response['subscription']);

        return $subscription;
    }

    /**
     * Reactive a cancelled, unpaid or trial-ended subscription.
     *
     * @param $id The Chargify subscription ID.
     * @param array $data Optional parameters according to docs.
     * @see http://docs.chargify.com/api-subscriptions
     */
    public function reactivate($id, $data = array()) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id . '/reactivate', $data, 'PUT');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Reset balance of a subscription to zero.
     *
     * @param $id The Chargify subscription ID.
     */
    public function resetBalance($id) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id . '/reset_balance', array(), 'PUT');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Add a coupon to a subscription.
     *
     * @param $id The Chargify subscription ID.
     * @param $code The coupon code.
     * @return  Updated subscription object on success.
     */
    public function addCoupon($id, $code) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id . '/add_coupon?code=' . $code, array(), 'POST');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Remove a coupon from a subscription.
     *
     * @param $id The Chargify subscription ID.
     * @param $code The coupon code.
     * @return  Updated subscription object on success.
     */
    public function removeCoupon($id, $code) {
        $subscription = null;

        $response = $this->request('subscriptions/' . $id . '/remove_coupon?code=' . $code, array(), 'DELETE');

        if (is_array($response) && is_array($response['subscription'])) {
            $subscription = new Resource($response['subscription']);
        }

        return $subscription;
    }

    /**
     * Migrate a subscription from one product to another
     *
     * @param $id The Chargify subscription ID.
     * @param $code The coupon code.
     * @return  Updated subscription object on success.
     */
    public function migrate( $id, $data = array() ) 
    {
        $response = $this->request('subscriptions/' . $id . '/migrations', $data, 'POST');
        $subscription = new Resource($response['subscription']);
        
        return $subscription;
    }

    /**
     * Issue an adjustment to the subscription
     *
     * @param $id The Chargify subscription ID.
     * @param $amount in cents
     * @param $memo optional description of why the memo was issued.
     * @param $method defines if the amount is a delta (default) or target amount.
     * @return  Updated subscription object on success.
     */
    public function adjust( $id, $amount, $memo='', $method=null ) 
    {
        $payload = [
            'amount_in_cents' => $amount,
            'memo' => $memo
        ];

        if ( $method ) $payload['method'] = $method;

        $response = $this->request('subscriptions/' . $id . '/adjustments', ['adjustment' => $payload], 'POST');
        $adjustment = new Adjustment( $response['adjustment'] );
        
        return $adjustment;
    }

}