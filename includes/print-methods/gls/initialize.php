<?php

include_once(plugin_dir_path(__FILE__) . 'gls-courier-printing-awb.php');

class InitializeGLS extends GLSGenereazaAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeGLS;
