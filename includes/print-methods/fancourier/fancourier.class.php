<?php 

class APIFanCourierClass
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

    function getLatestStatus($parameters)
	{
		$parameters = $parameters + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID,
			'display_mode' => 1,
		);
	
		$response = $this->callCourierMethod('awb_tracking_integrat.php', 'POST', $parameters);
		$response_message = explode(',', $response['message']);

		if($response_message[0] == -1) $result = NULL;
		else{
			$latest_status = $response_message[1];
			$latest_status_id = $response_message[0];
			$result = array($latest_status, $latest_status_id);
		}
		
		return $result; 
	}
}