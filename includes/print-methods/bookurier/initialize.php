<?php

include_once(plugin_dir_path(__FILE__) . 'bookurier-printing-awb.php');

class InitializeBookurier extends BookurierGenereazaAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeBookurier;
