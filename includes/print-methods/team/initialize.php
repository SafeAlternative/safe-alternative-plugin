<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'team-printing-awb.php');

class InitializeTeam extends TeamAWB
{
    public function __construct()
    {
        parent::__construct();
    }
}

new InitializeTeam;
