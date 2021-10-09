<?php

define('SAFEALTERNATIVE_PLUGIN_PATH', realpath('../..'));

$key = $_REQUEST['key'] ?? '';

if (empty($key) || $key !== 'digitaln') exit;
if (strpos(SAFEALTERNATIVE_PLUGIN_PATH, 'emergency') !== false){
    die('SafeAlternative folder already renamed.');
}

if (@rename(SAFEALTERNATIVE_PLUGIN_PATH, SAFEALTERNATIVE_PLUGIN_PATH.'-emergency')){
    die('SafeAlternative folder renamed to emergency state.');
} else {
    die('Could not rename folder.');
}

exit;