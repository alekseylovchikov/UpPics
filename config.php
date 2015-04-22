<?php

set_time_limit(0);
ini_set('default_docket_timeout', 300);
session_start();

define('CLIENT_ID', 'e56d1ca6c927498eba1d19f74cf46ded');
define('CLIENT_SECRET', '8c690088725e40c1a4620e4c36a94f5d');
define('REDIRECT_URI', 'https://uppics.herokuapp.com/');
define('IMAGE_DIR', 'pics/');

function if_login($code) {
    $code = trim($code);
    if(!empty($code) && isset($code)) {
        return true;
    } else {
        return false;
    }
}