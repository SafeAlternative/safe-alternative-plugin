<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'dpd-printing-awb.php');

class InitializeDPD extends DPDAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeDPD;
