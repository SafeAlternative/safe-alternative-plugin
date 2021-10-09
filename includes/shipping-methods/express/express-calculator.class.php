<?php

final class SafealternativeExpressShippingClass
{
    private $curl, $api_url;

    public function __construct()
    {
        $this->api_url = 'https://app.curiermanager.ro/cscourier/API/get_price?';
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }

    public function calculate(array $parameters): ?float
    {
        $parameters += [
            'api_user' => get_option('user_safealternative'),
            'api_pass' => get_option('password_safealternative'),
            'api_key' => get_option('express_key'),
        ];

        $this->api_url .= http_build_query($parameters);
        curl_setopt($this->curl, CURLOPT_URL, $this->api_url);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);

        $result = curl_exec($this->curl);
        $result = json_decode($result, true) ?? null;

        if(empty($result['error']) && $result['status'] == 'done') {
            $price = $result['data']['price'];
        } else throw new Exception('Can not calculate Express shipping: ' . json_encode($result));

        return $price;
    }
}
