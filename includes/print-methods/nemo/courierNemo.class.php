<?php 

class CourierNemo
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = 'https://app.nemoexpress.ro/nemo/API/get_status?';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function getLatestStatus($parameters)
    {
        $parameters += [
            'api_key' => get_option('nemo_key'),
        ];

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true) ?? null;

        if(!empty($result) && !empty($result['data']['status']) && $result['status'] == 'done') {
            $result = $result['data']['status'];
        } else $result = NULL;

        return $result;
    }

	
    public function printAwb($parameters)
    {
        $this->api_url = "https://app.nemoexpress.ro/nemo/API/print?";
        

        $parameters += [
            'api_key' => get_option('nemo_key')
        ];

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);

        return $result;
    }

    public function deleteAwb($parameters)
    {
        $this->api_url = "https://app.nemoexpress.ro/nemo/API/cancel?";
        

        $parameters += [
            'api_key' => get_option('nemo_key')
        ];

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);

        return $result;
    }
    public function get_services($main = true): ?array
    {
        $this->api_url = "https://app.nemoexpress.ro/nemo/API/list_services?";
        $parameters = [
            'api_key' => get_option('nemo_key'),
            'type' => $main ? 'main' : 'extra'
        ];

        $this->api_url .= http_build_query($parameters);

        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true) ?? null;

        if (!empty($result) && empty($result['error'])) {
            usort($result, function($a, $b) { return $a['id'] - $b['id'];});
        }
        
        return $result;
    }



}


class CourierNemoSafe
{
    private $curl;
    public $api_url;

    function __construct()
    {
        $this->api_url = SAFEALTERNATIVE_API_URL.'nemo/';
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