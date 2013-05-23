<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Site on Maintenance</title>
        <meta name="description" content="Site you are visiting is on maintenance mode, please come back later.">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="<?php echo asset_url('css/normalize.css'); ?>">
        <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
        <link rel="stylesheet" href="<?php echo asset_url('css/maintenance.css'); ?>">
        <script src="<?php echo asset_url('js/vendor/modernizr-2.6.2.min.js'); ?>"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="mainbox">
            <?php
                $title = kconfig ('system', 'maintenance_title', 'Website Offline');
                $desc = kconfig ('system', 'maintenance_body', 'We are still on maintenance, please come back later.');

                echo '<h1 class="mainbox-title">'.$title.'</h1>';

                echo '<div class="mainbox-body">'.$desc.'</div>';
            ?>
        </div>


        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo asset_url('js/vendor/jquery-1.10.2.min.js'); ?>"><\/script>')</script>
        <script src="<?php echo asset_url('js/plugins.js'); ?>"></script>
        <script src="<?php echo asset_url('js/main.js'); ?>"></script>

    </body>
</html>
