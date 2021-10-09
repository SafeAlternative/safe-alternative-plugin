<?php

final class SafealternativeBookurierClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = SAFEALTERNATIVE_API_URL . '/v1/shipping/bookurier/';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function CallMethod($url, $parameters = [], $verb = 'POST'): array
    {
        $url = $this->api_url . $url;
        $parameters += [
            'api_user' => get_option('user_safealternative'),
            'api_pass' => get_option('password_safealternative'),
            'userid' => get_option('bookurier_user'),
            'pwd' => get_option('bookurier_password'),
        ];
        $parameters = json_encode($parameters);

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($parameters)
        ));

        $result            = curl_exec($this->curl);
        $header            = curl_getinfo($this->curl);

        $output['message'] = $result;
        $output['debug'] = $url;
        $output['status'] = $header['http_code'];

        return $output;
    }
}
