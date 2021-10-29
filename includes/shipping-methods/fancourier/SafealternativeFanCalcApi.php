<?php
include_once 'SafealternativeFanClient.php';

class SafealternativeFanCalcApi extends SafealternativeFanClient
{
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

	function getTarif($params)
	{
		$parameters = $params + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID
		);

		$tarif = $this->callCourierMethod('tarif.php', $verb = 'POST', $parameters);
		return $tarif['message'];
	}

	function getCollectPoints($params)
	{
		$parameters = $params + array(
			'username' => $this->USERNAME,
			'user_pass' => $this->PASSWORD,
			'client_id' => $this->CLIENTID
		);

		$response = $this->callCourierMethod('export_locatii_collect_point_integrat.php', 'POST', $parameters);

		$collect_point_lines = str_getcsv($response['message'], "\n");

		$rows = array_map('str_getcsv', $collect_point_lines);
		$header = array_shift($rows);

		$csv = array();
		foreach ($rows as $row) {
			$csv[] = array_combine($header, $row);
		}

		return $csv;
	}

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