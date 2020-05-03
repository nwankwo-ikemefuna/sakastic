<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Paystack {

    private $secret;

    public function __construct() {
        $this->secret = 'paystack_secret_key_*****';
    }


    public function verify($reference) {
        if ( ! strlen($reference)) {
            json_response('Invalid reference!', false);
        }
        //verify url
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;
        $curl = curl_init();
        curl_setopt_array($curl, 
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "Authorization: Bearer " . $this->secret,
                    "cache-control: no-cache"
                ]
            ]
        );
        $request = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        //error with curl?
        if ($err) 
            return $this->respond(false, 'Gateway error encountered!');

        $result = json_decode($request, true);

        //error 
        if( ! $result)
            return $this->respond(false, 'Gateway unable to process transaction request!');

        //error with data: nothing came in
        if( ! $result['data'])
            return $this->respond(false, $result['message']);

        //data status not successful
        if($result['data']['status'] !== 'success')
            return $this->respond(false, 'Transaction was not successful: Last gateway response was: '.$result['data']['gateway_response']);

        //all goes well, everyone is happy...
        
        //update customer card details (with paystack of course) for subsequent transactions
        // $this->update_customer_details($result);

        return $this->respond(true, 'Transaction successful!');
    }


    private function update_customer_details($tranx) {
        $customer_id = $tranx["data"]["customer"]["customer_code"];
        curl_setopt_array($curl, 
            [
                CURLOPT_URL => 'https://api.paystack.co/customer/' . $customer_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => json_encode([
                    'metadata' => [
                        'accountNumber' => $tranx['data']['customer']['metadata']['accountNumber'],
                        'bankCode' => $tranx['data']['customer']['metadata']['bankCode'],
                        'bankName' => $tranx['data']['customer']['metadata']['bankName'],
                        'paidStatus' => '1'
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $this->secret,
                    "content-type: application/json",
                    "cache-control: no-cache"
                ]
            ]
        );

        $request = curl_exec($curl);
        $err = curl_error($curl);

        //error with curl?, Re-attemp
        if ($err) 
            $this->update_customer_details($tranx);

        $result = json_decode($request, true);

        //error, re-attempt
        if( ! $result['status'])
            $this->update_customer_details($tranx);

    }


    private function respond($status, $msg) {
        return ['status' => $status, 'msg' => $msg];
    }


}