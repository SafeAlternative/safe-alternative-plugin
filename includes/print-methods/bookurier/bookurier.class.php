<?php 

class BookurierClass
{
    protected $URL;
	protected $curl;

	function __construct()
	{
		$this->URL = 'https://www.bookurier.ro/colete/serv/get_stat.php?';
		$this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
	}

    function getLatestStatus()
	{
		$parameters = [
            'userid' => 'apitest',
            'pwd' => 'testapi',
            'msg' => '<?xml version="1.0" encoding="UTF-8"?><msg>
                        <cmd>
                            <awb>007140260614</awb>
                        </cmd>
                    </msg>'
        ];
        
        $this->URL .= http_build_query($parameters);
        // dd($this->URL);
        curl_setopt($this->curl, CURLOPT_URL, $this->URL);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($this->curl);
        dd($result);
		// $response = $this->callCourierMethod('get_stat.php', 'POST', $parameters);
		// dd($response);
		// return $result; 
	}
}