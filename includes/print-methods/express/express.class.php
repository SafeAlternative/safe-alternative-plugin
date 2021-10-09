<?php 

class APIExpressClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = 'https://app.curiermanager.ro/cscourier/API/get_status?';
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
            'api_key' => get_option('express_key'),
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
} 