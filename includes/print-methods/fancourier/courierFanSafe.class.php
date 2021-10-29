<?php

class CourierFanSafe
{
    private $curl;
    public $api_url;

    function __construct()
    {
        $this->api_url = SAFEALTERNATIVE_API_URL.'fan/';
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