<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('default_socket_timeout', 300);
session_start();

define('CLIENT_ID', 'e56d1ca6c927498eba1d19f74cf46ded');
define('CLIENT_SECRET', '8c690088725e40c1a4620e4c36a94f5d');
define('REDIRECT_URI', 'https://uppics.herokuapp.com/');
define('IMAGE_DIR', 'pics/');

/* FOR LOCAL TEST */
/*define('CLIENT_ID', '9bf3c45bc6e3465ba84ad501a9e7ff2d');
define('CLIENT_SECRET', 'bf3f243d6cb8470a94580aa0fed87c2c');
define('REDIRECT_URI', 'http://localhost:63342/phpbook/index.php');
define('IMAGE_DIR', 'pics/');*/

/*define('CLIENT_ID', '6dd0c078cb514f59a4b8c923e66a9d99');
define('CLIENT_SECRET', 'b8c5a16fe09e4bc59acf9d972b251c5a');
define('REDIRECT_URI', 'http://uppics.zz.mu/');
define('IMAGE_DIR', 'pics/');*/

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
    $url = "https://api.instagram.com/v1/users/{$user_id}/media/recent?client_id=" . CLIENT_ID . "&count=30";
    $instagram_info = connect_to_instagram($url);
    $results = json_decode($instagram_info, true);

    $pics = array();
    $big_pics = array();

    if(!empty($results['data']) && isset($results['data'])) {
      foreach($results['data'] as $result) {
          $pics[] = $result;
          $big_pics[] = $result['images']['standard_resolution']['url'];
      }

      // save_picture($big_pics, $user_name);

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
    $user_dir .= '/';

    if(!is_dir(IMAGE_DIR . $user_dir . '/')) {
      mkdir(IMAGE_DIR . $user_dir . '/', 0700);
    }

    foreach($imgs as $img) {
        $file_names[] = basename($img);
    }

    foreach($file_names as $file_name) {
        $destination[] = IMAGE_DIR . $user_dir . $file_name;
    }

    foreach($destination as $put) {
        file_put_contents($put, file_get_contents($imgs[$count]));
        $count++;
    }
}

// show all dirs
/*function show_dirs() {
  if($open_dir = opendir(IMAGE_DIR)) {
    while(($entry = readdir($open_dir)) !== false) {
      // echo $entry . '<br />';
    }

    closedir($open_dir);
  }
}*/

if(isset($_GET['code'])) {
  $_SESSION['code'] = $_GET['code'];
}

if(isset($_SESSION['code'])) {
  $_GET['code'] = $_SESSION['code'];
}

$str = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$reg = "/logout/";
if (preg_match($reg, $str)) {
  unset($_SESSION['code']);
  session_destroy();
  header("Location: /");
}

?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>UpPics</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
        <link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.4/lumen/bootstrap.min.css" rel="stylesheet" />
        <style>
          .pics {
            padding-bottom: 15px;
          }

          .img-shadow {
              box-shadow: 0 0 8px rgba(0,0,0,0.5);
              -webkit-box-shadow: 0 0 8px rgba(0,0,0,0.5);
              -moz-box-shadow: 0 0 8px rgba(0,0,0,0.5);
              -o-box-shadow: 0 0 8px rgba(0,0,0,0.5);
              padding: 10px;
          }
        </style>
    </head>
    <body>
        <!-- NAV BAR -->
        <nav class="navbar navbar-default">
            <div class="container-fluid container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php

                    if(isset($_SESSION['code'])) {
                      echo '<a class="navbar-brand" href="/">UpPics</a>';
                    } else {
                      echo '<a class="navbar-brand" href="/">UpPics</a>';
                    }

                    ?>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <?php

                        $code = isset($_GET['code']) ? $_GET['code'] : "";
                        $results = "";

                        if(if_login($code)) {
                            $url = "https://api.instagram.com/oauth/access_token";
                            $access_token_settings = array(
                                'client_id'     => CLIENT_ID,
                                'client_secret' => CLIENT_SECRET,
                                'grant_type'    => 'authorization_code',
                                'redirect_uri'  => REDIRECT_URI,
                                'code'          => $code
                            );

                            $curl = curl_init($url);

                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                            $result = curl_exec($curl);
                            curl_close($curl);

                            $results = json_decode($result, true);
                            $user_name = isset($results['user']['username']) ? $results['user']['username'] : "";

                            $get_user_pics = isset($_POST['user-name']) ? trim($_POST['user-name']) : "";

                            // get pics from user profile name
                            $user_id = get_user_id($get_user_pics);

                            echo '<li><a href="?q=logout">Выйти</a></li>';
                        } else {
                            echo '<li><a href="https://api.instagram.com/oauth/authorize/?client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&response_type=code">Войти</a></li>';
                        }

                        ?>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- END NAV BAR -->

        <article class="container">
            <header>
                <h1 class="text-center">UpPics</h1>
            </header>

            <section>
                <?php

                $output = "";

                if(isset($_GET['code'])) {
                  $output .= '<form action="/" method="post">';
                  $output .= '<p><input class="form-control" type="text" name="user-name" placeholder="Логин пользователя (например nala_cat)" /></p>';
                  $output .= '<p class="text-center"><button class="btn btn-success" type="submit" name="submit">Показать последние фото</button></p>';
                  $output .= '</form>';
                  echo $output;
                } else {
                  echo "<h1 class='alert alert-info'>Пожалуйста, войдите...</h1>";
                  echo '<section class="text-center"><a class="btn btn-success" href="https://api.instagram.com/oauth/authorize/?client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&response_type=code">Войти</a></section>';
                }

                ?>
            </section>

            <section class="row">
                <?php

                $get_pics = array();
                $errors = array();
                $show_error = "";

                if(if_login($code) && isset($user_id) && !empty($user_id)) {
                    $get_pics = show_images($user_id, $get_user_pics);

                    if($get_pics) {
                      echo '<header class="col-md-12">
                              <h1 class="text-center alert alert-success"><span class="glyphicon glyphicon-ok"></span> Загрузка последних фото завершена.</h1>
                            </header>';
                      foreach($get_pics as $pic) {
                          echo '<section class="col-md-12 pics text-center"><a href="' . $pic['images']['standard_resolution']['url'] . '" target="_blank"><img class="img-thumbnail img-responsive img-shadow" src="' . $pic['images']['standard_resolution']['url'] . '" alt="" /></a></section>';
                      }

                    } else {
                        $errors[] = 'Неверное имя пользователя!<br />';
                        $show_error .= "<h1 class='alert alert-danger'>";
                        foreach($errors as $error) {
                          $show_error .= $error;
                        }
                        $show_error .= '</h1>';
                        echo $show_error;
                    }
                }

                ?>
            </section>
        </article>
    </body>
</html>
