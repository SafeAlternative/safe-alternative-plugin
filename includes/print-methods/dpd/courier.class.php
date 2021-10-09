<?php

final class SafealternativeDPDClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = SAFEALTERNATIVE_API_URL . '/v1/shipping/dpd/';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function CallMethod($url, $parameters = [], $verb = 'POST'): array
    {
        $url = $this->api_url . $url;
        $parameters += [
            'api_user' => get_option('user_safealternative'),
            'api_pass' => get_option('password_safealternative'),
            'username' => get_option('dpd_username'),
            'password' => get_option('dpd_password'),
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

    public function get_services($awb = []): ?array
    {
        $this->api_url = "https://api.dpd.ro/v1/services";
        $parameters = [
            'username' => get_option('dpd_username'),
            'password' => get_option('dpd_password'),
        ];
        
        if (empty($awb)) {
            $this->api_url .= "?";
        } else {
            $this->api_url .= "/destination?";
            $parameters += $awb;
        }

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true)['services'] ?? null;
        
        if (!empty($result)) {
            usort($result, function($a, $b) { return $a['id'] - $b['id'];});
        }

        return $result;
    }

    public function get_senders(): ?array
    {
        if ($result = get_transient('dpd_sender_list')) return $result;
        
        $this->api_url = "https://api.dpd.ro/v1/client/contract?";
        $parameters = [
            'username' => get_option('dpd_username'),
            'password' => get_option('dpd_password'),
        ];
        
        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true)['clients'] ?? null;

        if (!empty($result)) {
            set_transient('dpd_sender_list', $result, DAY_IN_SECONDS);
        }

        return $result;
    }
}
