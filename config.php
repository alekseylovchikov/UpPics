<?php

set_time_limit(0);
ini_set('default_socket_timeout', 300);
session_start();

/*define('CLIENT_ID', 'e56d1ca6c927498eba1d19f74cf46ded');
define('CLIENT_SECRET', '8c690088725e40c1a4620e4c36a94f5d');
define('REDIRECT_URI', 'https://uppics.herokuapp.com/');
define('IMAGE_DIR', 'pics/');*/

/* LOCAL TEST */
define('CLIENT_ID', '9bf3c45bc6e3465ba84ad501a9e7ff2d');
define('CLIENT_SECRET', 'bf3f243d6cb8470a94580aa0fed87c2c');
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

function connect_to_instagram($url) {
    $ch = curl_init();

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function get_user_id($user_name) {
    $user_name = 't0.carrera';
    $url = "https://api.instagram.com/v1/users/search?q={$user_name}&client_id=" . CLIENT_ID;
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    return $results['data'][0]['id'];
}

function show_images($user_id) {
    // $url = "https://api.instagram.com/v1/users/{$user_id}/media/recent?client_id=" . CLIENT_ID . "&count=6";
    $url = "https://api.instagram.com/v1/users/{$user_id}/media/recent?client_id=" . CLIENT_ID . "&count=9";
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    $pics = array();
    $big_pics = array();

    foreach($results['data'] as $result) {
        $pics[] = $result;
        $big_pics[] = $result['images']['standard_resolution']['url'];
    }

    save_picture($big_pics);

    return $pics;
}

function save_picture($imgs = array()) {
    $file_names = array();
    $destination = array();
    foreach($imgs as $img) {
        $file_names[] = basename($img);
    }

    foreach($file_names as $file_name) {
        $destination[] = IMAGE_DIR . $file_name;
    }

    foreach($destination as $put) {
        file_put_contents($put, file_get_contents($img));
    }
}