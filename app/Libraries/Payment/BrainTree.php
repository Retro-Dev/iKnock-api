<?php
/**
 * Created by Arman Sheikh
 * Braintree SDK 3.27.0
 * Date: 1/26/2018
 * Time: 8:14 PM
 */
namespace  App\Libraries\Payment;

//defined('BASEPATH') OR exit('No direct script access allowed');

//require_once 'braintree_sdk/lib/Braintree.php';

class BrainTree
{
    public $is_error = false;
    public $message = '';

    function __construct()
    {
        //initialize configuration parameters
        /*Braintree_Configuration::environment('sandbox');
        Braintree_Configuration::merchantId('wq22yzvdb38m7chc');
        Braintree_Configuration::publicKey('fj6kg2zmx2z225n9');
        Braintree_Configuration::privateKey('67d6159559f36a923f789aa43a59d1af');*/

        \Braintree_Configuration::environment(config('services.braintree.environment'));
        \Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
        \Braintree_Configuration::publicKey(config('services.braintree.public_key'));
        \Braintree_Configuration::privateKey(config('services.braintree.private_key'));
    }

    public function clientToken($aCustomerId = 0)
    {
        if($aCustomerId)
            $param['customerId'] = $aCustomerId;

        $param['merchantAccountId'] = config('services.braintree.merchant_account_id');
        try {
            $clientToken = \Braintree_ClientToken::generate($param);
        } catch (\Exception $ex) {

            $response = $ex;
            $response->status = 'error';
            $this->is_error = true;
            $this->message = $ex->getMessage();
        }
        return $clientToken;
    }

    /**
     * For adding customer in braintree account
     * @params array(firstName, lastName, email, phone)
     * @return customer object or exception
     */
    public function addCustomer($params)
    {

        $response = (object)array();
        try {

            $result = \Braintree_Customer::create($params);

            if ($result->success) {
                $response = $result->customer;
                $response->status = 'success';
            } else {
                $response = $result;
                $response->status = 'error';
            }

        } catch (\Exception $ex) {
            $response = $ex;
            $response->status = 'error';
            $this->is_error = true;
            $this->message = $ex->getMessage();
        }

        return $response;
    }


    /**
     * For adding credit cards in customer braintree account
     * @params array(customerId, number, expirationDate, cvv)
     * @return card object or exception
     */
    public function addCard($params)
    {

        $response = (object)array();
        try {
            $result = \Braintree\CreditCard::create([
                'customerId' => $params['customerId'],
                'number' => $params['number'],
                'expirationDate' => $params['expirationDate'],
                'cvv' => $params['cvv'],
            ]);

            if ($result->success) {
                $response = $result;
                $response->status = 'success';
            } else {
                $response = $result;
                $response->status = 'error';
            }
        } catch (\Exception $ex) {
            $response = $ex;
            $response->status = 'error';
            $this->is_error = true;
            $this->message = $ex->getMessage();
        }

        return $response;
    }


    /**
     * For adding customer in braintree account
     * @params array(customerId, token)
     * @return customer object or exception
     */
    public function addPaymentMethod($params)
    {

        $response = (object)array();
        try {
            $result = \Braintree_PaymentMethod::create([
                'customerId' => $params['customerId'],
                'paymentMethodNonce' => $params['token'],
                'options' => [
                    'verifyCard' => true
                ]
            ]);
            if ($result->success) {
                $response = $result;
                $response->status = 'success';
            } else {
                $response = $result;
                $response->status = 'error';
            }
        } catch (\Exception $ex) {
            $response = $ex;
            $response->status = 'error';
            $this->is_error = true;
            $this->message = $ex->getMessage();
        }

        return $response;
    }


    /**
     * For adding customer in braintree account
     * @params array(customerId, amount)
     * @return customer object or exception
     */
    public function charge($param)
    {

        $response = (object)array();
        try {
            $result = \Braintree_Transaction::sale($param);
            if ($result->success) {

                $response = $result->transaction;
                $response->status = 'success';
            } else{
                $this->is_error = true;
                $this->message = $result->message;
                $response = $result->transaction;
            }
        } catch (\Exception $ex) {
            $this->is_error = true;
            $response = $ex;
            $this->message = $ex->getMessage();
            //$response->status = 'error';
        }
        return $result;
    }


    /**
     * For adding customer in braintree account
     * @params array(customerId, amount)
     * @return customer object or exception
     */
    public function customerSubscription($param)
    {

        $response = (object)array();
        //PaymentMethodNonce::find();
        try {
            $result = \Braintree_Subscription::create($param);
            if ($result->success) {

                $response = $result->subscription;
                $response->status = 'success';
            } else{
                $this->is_error = true;
                $this->message = $result->message;

                $response = $result->subscription;
                //$response->status = 'error';
            }
        } catch (\Exception $ex) {
            $this->is_error = true;
            $response = $ex;
            $this->message = $ex->getMessage();
            $response->status = 'error';
        }

        return $result;
    }

}
