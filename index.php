<?php include_once("config.php"); ?>

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
            padding: 5px;
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
                    <a class="navbar-brand" href="/phpbook/">UpPics</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Feed</a></li>
                    </ul>
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

                            echo '<li><a href="#">' . $user_name . '</a></li>';
                            echo '<li><a href="/">Logout</a></li>';
                        } else {
                            echo '<li><a href="https://api.instagram.com/oauth/authorize/?client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI . '&response_type=code">Login</a></li>';
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
                  $output .= '<form action="/phpbook/?code=<?= $code ?>" method="post">';
                  $output .= '<p><input class="form-control" type="text" name="user-name" placeholder="Instagram user profile name" /></p>';
                  $output .= '<p class="text-center"><button class="btn btn-success" type="submit" name="submit">Get images</button></p>';
                  $output .= '</form>';
                  echo $output;
                } else {
                  echo "<h1 class='alert alert-info'>Please, login...</h1>";
                }

                ?>
            </section>

            <section class="row">
                <?php

                $get_pics = array();

                if(if_login($code) && isset($user_id) && !empty($user_id)) {
                    $get_pics = show_images($user_id);

                    foreach($get_pics as $pic) {
                        echo '<section class="col-md-4 pics"><a href="' . $pic['images']['standard_resolution']['url'] . '" target="_blank"><img class="img-thumbnail img-responsive" src="' . $pic['images']['standard_resolution']['url'] . '" alt="" /></a></section>';
                    }
                }

                ?>
            </section>
        </article>
    </body>
</html>
