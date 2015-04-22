<?php

set_time_limit(0);
ini_set('default_socket_timeout', 300);
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
    $url = "https://api.instagram.com/v1/users/search?={$user_name}&client_id=" . CLIENT_ID;
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    return $instagram_info['data'][0]['id'];
}

function show_images($user_id) {
    $url = "https://api.instagram.com/v1/users/{$user_id}/media/recent?client_id=" . CLIENT_ID . "&count=5";
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    foreach($results['data'] as $result) {
        $image_url = $result['images']['low_resolution']['url'];
        echo "<img src='{$image_url}' alt='' /><br />";
    }
}