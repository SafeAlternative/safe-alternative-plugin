<?php 

class CourierFan
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


    ////////////////////////////////////////////////////////////
    // TRACK & TRACE ///////////////////////////////////////////
    ////////////////////////////////////////////////////////////
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


    ////////////////////////////////////////////////////////////
    // PRINTARE AWB ////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    function printAwb($params) {


		$parameters = $params + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID
		);

        $html_pdf   = $parameters['html_pdf'];
        $api_url = 'view_awb_integrat_pdf.php';
        if ($html_pdf == 'html') {
            $api_url = 'view_awb_integrat.php';
        }

        
		$response = $this->callCourierMethod($api_url, 'POST', $parameters);
		return $response;      
        
    }


    ////////////////////////////////////////////////////////////
    // DELETE AWB //////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
    function deleteAwb($params) {


		$parameters = $params + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID
		);

        $api_url = 'delete_awb_integrat.php';
        
		$response = $this->callCourierMethod($api_url, 'POST', $parameters);
		return $response;      
        
    }


    ////////////////////////////////////////////////////////////
    // SERVICES ////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////
	function getServices($params = array())
	{
		$parameters = $params + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID
		);

		if (file_exists('export_servicii_integrat.csv')) {
			unlink('export_servicii_integrat.csv');
		}
		$export_servicii_integrat = $this->callCourierMethod('export_servicii_integrat.php', $verb = 'POST', $parameters);
		$export_servicii_integrat = $export_servicii_integrat['message'];

		$handle = fopen('export_servicii_integrat.csv', 'w');
		fwrite($handle, $export_servicii_integrat);
		fclose($handle);

		$rows = array_map('str_getcsv', file('export_servicii_integrat.csv'));
		$header = array_shift($rows);
		$csv = array();
		foreach ($rows as $row) {
			$csv[] = array_combine($header, $row);
		}

		foreach ($csv as $k => $val) {
			$fis[$val["Servicii FAN Courier"]] = $val["Servicii FAN Courier"];
		}

		return $fis;
	}


	/////////////////////////////////////////////////////////////
	///// PICKUP POINTS /////////////////////////////////////////
	//////////////////////////////////////////////////////////////
	function getClientIds()
	{
		$parameters = array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
		);

		$response = $this->callCourierMethod('get_account_clients_integrat.php', 'POST', $parameters);
		return json_decode($response['message']);
	}


}