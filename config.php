<?php

set_time_limit(0);
ini_set('default_docket_timeout', 300);
session_start();

define('CLIENT_ID', '4724aca56eba4a08bd561a249664e42f');
define('CLIENT_SECRET', '5e6ff4ab7e16449d89e5659556f4d3f5');
define('REDIRECT_URI', 'http://localhost:63342/phpbook/index.php');
define('IMAGE_DIR', 'pics/');

function if_login($code) {
    $code = trim($code);
    if(!empty($code) && isset($code)) {
        return true;
    } else {
        return false;
    }
}