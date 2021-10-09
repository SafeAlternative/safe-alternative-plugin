<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'memex-printing-awb.php');

class InitializeMemex extends MemexAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeMemex;
