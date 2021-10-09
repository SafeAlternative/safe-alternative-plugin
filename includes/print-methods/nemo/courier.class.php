<?php

final class SafealternativeNemoClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = SAFEALTERNATIVE_API_URL .'/v1/shipping/nemo/';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
    }

    public function CallMethod($url, $parameters = [], $verb = 'POST'): array
    {
        $url = $this->api_url . $url;
        
        $parameters += [
            'api_user' => get_option('user_safealternative'),
            'api_pass' => get_option('password_safealternative'),
            'api_key' => get_option('nemo_key'),
        ];
        
        $parameters = json_encode($parameters);
        
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($parameters)
        ));
        
        $result = curl_exec($this->curl);
        $header = curl_getinfo($this->curl);
        
        $output['message'] = $result;
        $output['debug'] = $url;
        $output['status'] = $header['http_code'];
        
        return $output;
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
