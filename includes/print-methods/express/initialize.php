<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'express-printing-awb.php');

class InitializeExpress extends ExpressAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeExpress;
