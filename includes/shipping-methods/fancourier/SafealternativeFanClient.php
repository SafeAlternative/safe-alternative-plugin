<?php

class SafealternativeFanClient
{
	protected $URL;
	protected $USERNAME;
	protected $CLIENTID;
	protected $PASSWORD;
	protected $curl;

	function __construct($user, $pass, $clientid = null)
	{
		$this->URL = 'https://www.selfawb.ro';
		$this->USERNAME = rawurldecode($user);
		$this->PASSWORD = rawurldecode($pass);
		$this->CLIENTID = $clientid;
		$this->curl = curl_init();
	}

	function callCourierMethod($function, $verb, $parameters)
	{
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 100);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 100);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);

		curl_setopt($this->curl, CURLOPT_URL, $this->URL . '/' . $function);

		$result = curl_exec($this->curl);
		$header = curl_getinfo($this->curl);

		$output['message'] = $result;
		$output['status'] = $header['http_code'];

		return $output;
	}
}