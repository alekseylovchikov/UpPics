<?php

set_time_limit(0);
ini_set('default_socket_timeout', 300);
session_start();

/*define('CLIENT_ID', 'e56d1ca6c927498eba1d19f74cf46ded');
define('CLIENT_SECRET', '8c690088725e40c1a4620e4c36a94f5d');
define('REDIRECT_URI', 'https://uppics.herokuapp.com/');
define('IMAGE_DIR', 'pics/');*/

/* FOR LOCAL TEST */
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
    if(isset($user_name) && !empty($user_name)) {
      $url = "https://api.instagram.com/v1/users/search?q={$user_name}&client_id=" . CLIENT_ID;
      $instagram_info = connect_to_instagram($url);
      $results = json_decode($instagram_info, true);

      return $results['data'][0]['id'];
    }
}

// show all images
function show_images($user_id, $user_name) {
    $url = "https://api.instagram.com/v1/users/{$user_id}/media/recent?client_id=" . CLIENT_ID . "&count=12";
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    $pics = array();
    $big_pics = array();

    if(!empty($results['data']) && isset($results['data'])) {
      foreach($results['data'] as $result) {
          $pics[] = $result;
          $big_pics[] = $result['images']['standard_resolution']['url'];
      }

      save_picture($big_pics, $user_name);

      return $pics;
    } else {
      return false;
    }
}

// save pics in dirs
function save_picture($imgs = array(), $user_dir) {
    $file_names = array();
    $destination = array();
    $count = 0;
    $count_dirs = 1;

    if(!is_dir(IMAGE_DIR . $user_dir . '/')) {
      mkdir(IMAGE_DIR . $user_dir . '/', 0700);
    }

    foreach($imgs as $img) {
        $file_names[] = basename($img);
    }

    foreach($file_names as $file_name) {
        $destination[] = IMAGE_DIR . $user_dir . '/' . $file_name;
    }

    foreach($destination as $put) {
        file_put_contents($put, file_get_contents($imgs[$count]));
        $count++;
    }
}

// show all dirs
function show_dirs() {
  if($open_dir = opendir(IMAGE_DIR)) {
    while(($entry = readdir($open_dir)) !== false) {
      // echo $entry . '<br />';
    }

    closedir($open_dir);
  }
}
