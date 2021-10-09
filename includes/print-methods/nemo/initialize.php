<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'nemo-printing-awb.php');

class InitializeNemo extends NemoAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeNemo;
