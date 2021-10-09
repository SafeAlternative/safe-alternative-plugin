<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'sameday-printing-awb.php');

class InitializeSameday extends SamedayAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeSameday;
