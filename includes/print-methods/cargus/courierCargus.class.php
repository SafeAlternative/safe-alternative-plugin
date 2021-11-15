<?php

class CourierCargus
{
	private $curl;
	private $url;
	private $key;

	function __construct($url = '', $key = '')
	{
		$this->url = $url;
		$this->key = $key;
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
	}

	function CallMethod($function, $parameters = "", $verb, $token = null)
	{
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
		curl_setopt($this->curl, CURLOPT_URL, $this->url . '/' . $function);

		$subscriptionKey = 'Ocp-Apim-Subscription-Key:' . $this->key;
		if (!empty($token->Error)) {
			return wp_die("Va rugam verificati datele de autentificare ale contului UrgentCargus.");
		}

		if ($function == "LoginUser") {
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array($subscriptionKey, 'Ocp-Apim-Trace:true', 'Content-Type: application/json', 'ContentLength:' . strlen($parameters)));
		} else {
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array($subscriptionKey, 'Ocp-Apim-Trace:true', 'Authorization: Bearer ' . $token, 'Content-Type: application/json', 'Content-Length: ' . strlen($parameters)));
		}

		$result = curl_exec($this->curl);
		$header = curl_getinfo($this->curl);
		$output['message'] = $result;
		$output['status'] = $header['http_code'];

		return $output;
	}
}


class CourierCargusSafe
{
	private $curl;
	public $api_url;

	function __construct()
	{
		$this->api_url = SAFEALTERNATIVE_API_URL.'cargus/';
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
	}

	function CallMethod($url, $parameters = "", $verb = 'POST')
	{
		$parameters_json = json_encode($parameters);
		
		$url = $this->api_url . $url;
		//var_dump($parameters);exit;

		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters_json);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($parameters_json),
			'Authorization: Bearer ' . $parameters['token'],
		));
		
		$result            = curl_exec($this->curl);
		$header            = curl_getinfo($this->curl);

		//($result);exit;
		$result=json_decode($result);
		$output['success'] = $result->success;
		$output['message'] = $result->message;    
		$output['status'] = $header['http_code'];
		return $output;
	}
}

