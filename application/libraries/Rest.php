<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Rest {

	private $ci;

	public function __construct() {
		$this->ci =& get_instance();
	}


	private function curl($url, $data = [], $is_post = true) {
		$data_string = http_build_query($data);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		if ($is_post) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, 
				[
				    'Content-Type: application/x-www-form-urlencoded',
				    'Content-Length: ' . strlen($data_string)
				]
			);
			curl_setopt($curl, CURLOPT_POST, 1);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
	    }
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
    }


	public function get($url, $data = []) {
		echo $this->curl($url, $data, false);
    }


    public function post($url, $data) {
		echo $this->curl($url, $data);
    }

}