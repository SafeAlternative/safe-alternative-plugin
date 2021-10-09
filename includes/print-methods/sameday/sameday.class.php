<?php

class APISamedayClass
{
    private $sameday, $sameday_client;

    public function __construct()
    {
        $this->sameday_client = new SafeAlternative\Sameday\SamedayClient(
            get_option('sameday_username', ''),
            get_option('sameday_password', '')
        );
        $this->sameday = new SafeAlternative\Sameday\Sameday($this->sameday_client);
    }

    public function getLatestStatus($awb)
    {
        try{
            $response = $this->sameday->getAwbStatusHistory(
                new SafeAlternative\Sameday\Requests\SamedayGetAwbStatusHistoryRequest($awb)
            );
            return $response->getHistory()[0]->getState();
        } catch (\Exception $e) {
            return $response = NULL;
        }
    }
}