<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'urgentcargus-courier-printing-awb.php');

class InitializeCargus extends CargusAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeCargus;
