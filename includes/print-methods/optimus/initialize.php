<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'optimus-printing-awb.php');

class InitializeOptimus extends OptimusAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeOptimus;
