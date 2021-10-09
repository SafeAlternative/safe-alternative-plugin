<?php

final class SafealternativeDPDShippingClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = 'https://api.dpd.ro/v1/calculate?';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function calculate(array $parameters): ?float
    {
        $parameters += [
            'username' => get_option('dpd_username'),
            'password' => get_option('dpd_password'),
        ];

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true)['calculations'] ?? null;

        if (!empty($result) && !isset($result[0]['error'])) {
            $result = $result[0]['price']['total'] ?? null;
        } else {
            throw new Exception('Can not calculate DPD shipping: ' . json_encode($result));
        }

        return $result;
    }
}
