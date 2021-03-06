<?php

class SafealternativeGLSClass
{
	private $curl;

	function __construct()
	{
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
	}

	function CallMethod($path, $parameters = "", $verb = 'POST')
	{
		$api_username   =  rawurlencode(get_option('user_safealternative'));
		$api_password   =  rawurlencode(get_option('password_safealternative'));
		$user           =  rawurlencode(get_option('GLS_user'));
		$password       =  rawurlencode(get_option('GLS_password'));
		$url = SAFEALTERNATIVE_API_URL . '/shipping/gls/' . $path . '/' . $api_username . "/" . $api_password . "/" . $user . "/" . $password;

		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($parameters)));

		$result = curl_exec($this->curl);
		$header = curl_getinfo($this->curl);
		$output['message'] = $result;
		$output['status'] = $header['http_code'];
		return $output;
	}
}
