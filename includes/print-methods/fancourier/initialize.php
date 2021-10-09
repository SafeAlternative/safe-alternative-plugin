<?php

include_once(plugin_dir_path(__FILE__) . 'fan-courier-printing-awb.php');

class InitializeFan extends FanGenereazaAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeFan;
