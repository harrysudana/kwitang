<?php

include_once FRONT_PATH.'default/views/inc/functions.php';

/* buld navigation menu */
function __nav($st) {
  $ret = '';

  if (empty($st)) {
    return $ret;
  }

  foreach ($st as $s) {
    if (empty ($s->childs)) {
      $ret .= '<li><a href="'.structure_url($s).'">'.var_lang($s->title).'</a></li>';
    } else {
      $ret.= '<li class="dropdown">'
            .'<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.var_lang($s->title).' <b class="caret"></b></a>'
            .'<ul class="dropdown-menu">';
      $tmp = get_structure_sct($s->id);
      if ( ! empty($tmp) && is_array($tmp)) {
        $ret.= '  <li class="nav-header"><a href="'.structure_url($s).'">'.var_lang($s->title).'</a></li>'
              .'  <li class="nav-divider"></li>';
      }
      $ret.= __nav($s->childs)
            .'</ul>'
            .'</li>';
    }
  }
  return $ret;
}

$all_structure = get_structure();

$head_title    = empty ($head_title) ? kconfig ('system', 'site_name') : kconfig ('system', 'site_name').' | '.$head_title;
$head_desc     = empty ($head_desc) ? kconfig ('system', 'site_slogan') : $head_desc;
$head_keywords = empty ($head_keywords) ? kconfig ('system', 'site_keywords') : $head_keywords;

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width">
  <?php
  echo '<title>'.$head_title.'</title>'."\r\n";
  echo '  <meta name="description" content="'.$head_desc.'" />'."\r\n";
  echo '  <meta name="keywords" content="'.$head_keywords.'" />'."\r\n";
  $_favicon = kconfig ('system', 'site_favicon');
  if ( ! empty($_favicon)) {
    echo '<link rel="shortcut icon" href="'.$_favicon.'" type="image/x-icon">';
  }

  $_swatch = kconfig ('system', 'css_style');
  if ( ! empty ($_swatch)) {
    echo '<link rel="stylesheet" href="'.asset_url('swatch/'.$_swatch.'.min.css').'">';
  } else {
    echo '<link rel="stylesheet" href="'.asset_url('css/bootstrap.min.css').'">';
    echo '<link rel="stylesheet" href="'.asset_url('css/bootstrap-theme.min.css').'">';
  }

  $panel = kconfig ('system', 'default_panel');
  if ( $panel == 'panel-noborder') {
    echo '<link rel="stylesheet" href="'.asset_url('css/panel-noborder.css').'">';
  }

  $style_css = '';

  $default_maxwidth = (int) kconfig ('system', 'default_maxwidth', 'auto');
  if ( is_integer($default_maxwidth) && $default_maxwidth > 300) {
    $style_css .= '@media (min-width: 1200px) {.container {width: '.$default_maxwidth.'px; } }';
  }
  $def_css_script = kconfig ('system', 'default_css_override');
  if ( ! empty ($def_css_script)) {
    $style_css .= $def_css_script;
  }

  if ( ! empty ($style_css)) {
    echo '<style>'.$style_css.'</style>';
  }
  ?>

  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('video-js/video-js.min.css'); ?>">

  <script src="<?php echo asset_url('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js'); ?>"></script>
  <script src="<?php echo asset_url('video-js/video.js'); ?>"></script>
  <script>
    videojs.options.flash.swf = "<?php echo asset_url('video-js/video-js.swf'); ?>";
  </script>
</head>
<body>
  <!--[if lt IE 7]>
      <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
  <![endif]-->

  <?php
    $fb_appid = kconfig ('system', 'fb_appid');
    if ( ! empty ($fb_appid)) {
      echo '<div id="fb-root"></div>
            <script>(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/id_ID/all.js#xfbml=1&appId='.$fb_appid.'";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));</script>';
    }
  ?>

  <div class="container">
    <div class="row">
      <div class="col-md-12 small">
        <div class="pull-left" style="padding-top:4px;">
          <?php echo kdate(); ?>
        </div>
        <div class="pull-right">
          <?php if ( kconfig ('system', 'default_search_top', 'no') == 'yes') { ?>
          <form role="search" action="<?php echo site_url('search'); ?>" method="get">
                <input name="q" type="text" class="" placeholder="Pencarian...">
                  <input type="submit" value="Cari">
          </form>
          <?php } ?>
        </div>
        <div class="clearfix"></div>
        <div class="bg-primary" style="height:1px"></div>
      </div>

      <div class="col-md-12">
        <?php
          $site_logo = kconfig ('system', 'site_logo');
          if ( ! empty ($site_logo)) {
            echo '<div class="logo">
                    <a href="'.site_url().'">
                      <img src="'.$site_logo.'" alt="'.kconfig ('system', 'site_name').'">
                    </a>
                  </div>';
          }
        ?>
        <div class="logo-text">
        <?php
          echo '<h2>'.kconfig ('system', 'site_name').'</h2>'
              .'<h3><i>'.kconfig ('system', 'site_slogan').'</i></h3>';
        ?>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <nav class="navbar <?php echo kconfig ('system', 'default_navbar', 'navbar-default'); ?>" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo site_url(); ?>"><span class="glyphicon glyphicon-home"></span></a>
        </div>
        <div class="collapse navbar-collapse" id="main-navbar">
          <ul class="nav navbar-nav">
            <?php
            echo __nav($all_structure);
            ?>
          </ul>

          <?php if ( kconfig ('system', 'default_search_nav', 'yes') == 'yes') { ?>
          <form class="navbar-form navbar-right" role="search" action="<?php echo site_url('search'); ?>" method="get">
            <div class="form-group">
              <input name="q" type="text" class="form-control input-search" placeholder="Pencarian...">
            </div>
          </form>
          <?php } ?>
        </div>
      </div>
    </nav>
  </div>

  <div class="container">
    <?php
      if ( ! empty ($current_structure)) {
        $bre = get_breadcrumb ($current_structure);
        if ( ! empty ($bre)) {
          echo '<ol class="breadcrumb">';
          echo '<li><a href="'.site_url().'"><span class="glyphicon glyphicon-home"></span></a></li>';
          foreach ($bre as $value) {
            echo '<li><a href="'.structure_url($value).'">'.var_lang($value->title).'</a></li>';
          }

          if ( ! empty ($content->title)) {
            echo '<li>'.character_limiter($content->title, 40).'</li>';
          } else {
            if ( isset ($current_sct)) {
              echo '<li>Indeks '.var_lang($current_sct->title).'</li>';
            } else {
              echo '<li>Indeks</li>';
            }
          }

          $sct_in_st = get_structure_sct($current_structure->id);
          $sct_in_st_jml = count ($sct_in_st);
          if ($sct_in_st_jml > 1) {
            echo '<ul class="pull-right nav nav-pills" style="margin-top:-6px">';
            foreach ($sct_in_st as $value) {
              echo '<li><a href="'.index_url($value).'" class="label label-info">'.var_lang($value->title).'</a></li>';
            }
            echo '</ul>';
          }

          echo '</ol>';
        }
      }
    ?>
  </div>


