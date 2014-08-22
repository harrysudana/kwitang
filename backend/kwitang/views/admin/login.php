<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');
$css_files = array(
    asset_url ('css/bootstrap.min.css', false),
    asset_url ('css/admin.min.css', false)
);

$js_files = array(
    asset_url ('js/jquery-1.7.2.min.js', false),
    asset_url ('js/modernizr-2.6.1-respond-1.1.0.min.js', false),
    asset_url ('js/bootstrap.min.js', false),
    asset_url ('js/admin.min.js', false)
);
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">

    <title>Admin | <?php echo ( ! empty ( $head_title)) ? $head_title : kconfig ('system', 'site_name', 'Kwitang NCMS'); ?></title>

    <?php
    // Jika ada, keluarkan css_files
    if ( ! empty ( $css_files) AND is_array ( $css_files)) {
        foreach ( $css_files as $file) {
            echo '<link rel="stylesheet" type="text/css" href="'.$file.'">'."\n";
        }
    }

    // Jika ada, keluarkan js_files dan script
    if ( ! empty ( $js_files) AND is_array ( $js_files)) {
        foreach ( $js_files as $file) {
            echo '<script type="text/javascript" src="'.$file.'"></script>'."\n";
        }
    }

    if ( ! empty ( $script)) echo '<script type="text/javascript">'.$script.'</script>';
    ?>
    <style type="text/css">
    body {
        margin: 0;
        padding: 0;
        background-color: #3e3e3f;
        color: #FFFFFF;
    }

    .container {
        width: auto;
        margin-top: 120px;
    }
    .boks {
        max-width: 380px;
        padding: 10px 8px;
        margin: 0 auto;
    }
    .form-signin {
        width: auto;
        color: #212121;
        max-width: 380px;
        padding: 14px 30px 10px;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    .form-signin .form-signin-heading {
        margin-bottom: 10px;
    }
    .form-signin .control-label {
        width: 32px;
        text-align: left;
    }
    .form-signin .controls {
        margin-left: 35px;
    }
    .logo {
        height: 64px;
        margin-bottom: 10px;
    }
    </style>
</head>
<body>
    <div id="notify" class="alert" style="display:none;"></div>
    <div class="container">
        <div class="boks">
            <?php echo form_open(site_url ('auth/validate'), 'id="form-signin" class="form-horizontal form-signin"'); ?>
                <?php
                $logo = kconfig ('system', 'site_logo', '');
                if ($logo != '') {
                    echo '<img class="logo pull-right" src="'.$logo.'" alt="">';
                }
                ?>
                <h4 class="form-signin-heading"><?php echo kconfig ('system', 'site_name', 'Kwitang NCMS'); ?></h4>
                <hr>
                <?php
                if (isset ($_GET['failed'])) {
                    echo '<div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Error!</strong> ';
                    if ($_GET['failed'] == 1) {
                        echo lang('k_user_not_exist');
                    } else {
                        echo lang('k_wrong_password');
                    }
                    echo '</div>';
                }
                ?>
                <div class="clearfix"></div>
                <div class="control-group">
                    <label class="control-label">
                        <i class="icon icon-user"></i>
                    </label>
                    <div class="controls">
                        <input type="text" name="username" id="username" placeholder="Username" class="input-block-level">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">
                        <i class="icon icon-lock"></i>
                    </label>
                    <div class="controls">
                        <input type="password" name="password" id="password" class="input-block-level" placeholder="Password">
                    </div>
                </div>
                <div class="control-group">

                    <div class="controls">
                        <label class="checkbox pull-left">
                            <input type="checkbox" name="remember" value="1" checked="checked">
                            <?php echo lang('k_remember'); ?>
                        </label>
                        <button class="pull-right btn btn-primary" type="submit" id="btnLogin"><?php echo lang('k_login'); ?></button>
                    </div>
                </div>
                <div id="loginanim" class="progress progress-striped active" style="display:none;">
                    <div class="bar" style="width: 100%;"></div>
                </div>
                <div class="clearfix"></div>
            <?php echo form_close(); ?>
        </div>
        <div class="boks">
            <p class="txt-right">
                <small>
                    <?php
                        $foot_text = kconfig ('system', 'admin_footer_text');
                        if ( empty ($foot_text)) {
                            $foot_text = lang ('k_admin_footer_text');
                        }

                        echo $foot_text;
                    ?>
                </small>
            </p>
        </div>
    </div>



    <script type="text/javascript">
    var loginText = '';
    $(document).ready(function() {
        $('#username').focus();

        $('#form-signin').submit(function(e) {
            e.preventDefault();

            loginText = $('#btnLogin').text();
            $('#btnLogin').text(loginText + ' ...');

            if ($('#username').val().trim().length == 0 || $("#password").val().trim().length==0) {
                $("#username").focus();
                return false;
            }
            $('#loginanim').show();

            var post_data = new Object();
            post_data.username = $('#username').val();
            post_data.password = $('#password').val();

            if (typeof($('input[name=remember]:checked').val()) != 'undefined') {
                post_data.remember = 1;
            }

            <?php
            $csrf_protection = $this->config->item('csrf_protection');
            $csrf_token_name = $this->config->item('csrf_token_name');
            if ($csrf_protection) {
                echo 'post_data.'.$csrf_token_name.' = $(\'input[name="'.$csrf_token_name.'"]\').val();';
            }
            ?>

            $.post($(this).attr('action'),
                    post_data,
                    function (data) {
                        $('#loginanim').hide();
                        $('#btnLogin').text(loginText);

                        if ( ! data.status) {
                            notify(data.message, true);
                            $('#password').focus();
                        } else {
                            $('#form-signin').html('<p>Redirecting...</p>');
                            window.location.href = data.redirect_url;
                        }
                    });
        });
    });
    </script>
</body>
</html>
