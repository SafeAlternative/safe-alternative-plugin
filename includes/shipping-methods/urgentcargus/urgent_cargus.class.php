<?php

class SmartUrgentCourier
{
	protected $URL;
	protected $USERNAME;
	protected $KEY;
	protected $PASSWORD;
	protected $curl;

	function __construct($url, $key, $user, $pass)
	{
		$this->URL = $url;
		$this->KEY = $key;
		$this->USERNAME = $user;
		$this->PASSWORD = $pass;
		$this->curl = curl_init();
	}

	function callCourierMethod($function, $verb, $parameters, $token = '')
	{
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
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

///////////////////////////////////////////////////////
//// URGENT CARGSUS CLASS /////////////////////////////
///////////////////////////////////////////////////////

class UrgentcargusCourier extends SmartUrgentCourier
{
	private $token;

	function callCourierMethod($function, $verb, $parameters, $token = '')
	{
		$subscriptionKey = 'Ocp-Apim-Subscription-Key:' . $this->KEY;

		if ($function == "LoginUser") {
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array($subscriptionKey, 'Ocp-Apim-Trace:true', 'Content-Type: application/json', 'ContentLength:' . strlen($parameters)));
		} else {
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array($subscriptionKey, 'Ocp-Apim-Trace:true', 'Authorization: Bearer ' . $token, 'Content-Type: application/json', 'Content-Length: ' . strlen($parameters)));
		}

		return parent::callCourierMethod($function, $verb, $parameters, $token = '');
	}

	function getToken()
	{
		$fields = array('UserName' => $this->USERNAME, 'Password' => $this->PASSWORD);
		$json = json_encode($fields);

		$login = $this->callCourierMethod('LoginUser', 'POST', $json);

		if ($login['status'] != "200") {
			$this->token = NULL;
		} else {
			$token = json_decode($login['message']);
			$this->token = $token;
		}

		return $token ?? null;
	}

	function getLocalities()
	{
		$tok = $this->getToken();

		$localList = array();
		$nrLocal = 0;

		$countiesNameList = safealternative_get_counties_list();

		$countiesList =  array("3" => "Alba", "4" => "Arad", "5" => "Arges", "6" => "Bacau", "7" => "Bihor", "8" => "Bistrita-Nasaud", "9" => "Botosani", "10" => "Braila", "11" => "Brasov", "12" => "Buzau", "13" => "Calarasi", "14" => "Caras-Severin", "15" => "Cluj", "16" => "Constanta", "17" => "Covasna", "18" => "Dambovita", "19" => "Dolj", "20" => "Galati", "21" => "Giurgiu", "22" => "Gorj", "23" => "Harghita", "24" => "Hunedoara", "25" => "Ialomita", "26" => "Iasi", "27" => "Ilfov", "28" => "Maramures", "29" => "Mehedinti", "30" => "Mures", "31" => "Neamt", "32" => "Olt", "33" => "Prahova", "34" => "Salaj", "35" => "Satu Mare", "36" => "Sibiu", "37" => "Suceava", "38" => "Teleorman", "39" => "Timis", "40" => "Tulcea", "41" => "Valcea", "42" => "Vaslui", "43" => "Vrancea", "44"  => "Bucuresti");

		for ($i = 3; $i <= 44; $i++) {
			$resultLocalities = $this->callCourierMethod('Localities?countryId=1&countyId=' . $i . '', 'GET', $json = "", $tok);

			if ($resultLocalities['status'] != "200") {
			} else {

				$jsonLocalitiesList = $resultLocalities['message'];
				$localitiesList = json_decode($jsonLocalitiesList, true);

				foreach ($localitiesList as $locality) {
					$localList[$nrLocal]['LocalityName'] = $locality['Name'];
					$localList[$nrLocal]['LocalityId'] = $locality['LocalityId'];
					$localList[$nrLocal]['LocalityKm'] = $locality['ExtraKm'];
					$localList[$nrLocal]['LocalityParent'] = $locality['ParentName'];
					$localList[$nrLocal]['LocalityCounty'] = $countiesList[$locality['CountyId']];
					$localList[$nrLocal]['LocalityCountyCode'] = $countiesNameList[$countiesList[$locality['CountyId']]];

					$nrLocal++;
				} //end foreach

			} //end else
		} // end for
		return $localList;
	} // end function

}//end class
