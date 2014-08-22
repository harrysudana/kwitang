<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

$admin_css_files = array(
    asset_url ('css/jquery-ui-1.8.23.custom.css', false),
    asset_url ('css/bootstrap.min.css', false),
    asset_url ('css/bootstrap-responsive.min.css', false),
    asset_url ('css/bootstrap-datetimepicker.min.css', false),
    asset_url ('css/admin.min.css', false)
);

$admin_js_files = array(
    asset_url ('js/jquery-1.7.2.min.js', false),
    asset_url ('js/modernizr-2.6.1-respond-1.1.0.min.js', false),
    asset_url ('js/jquery-ui-1.8.23.custom.min.js', false),
    asset_url ('js/bootstrap.min.js', false),
    asset_url ('js/bootstrap-datetimepicker.min.js', false),
    asset_url ('dtables/js/jquery.dataTables.min.js', false),
    asset_url ('js/dtablebootstrap.js', false),
    asset_url ('js/admin.min.js', false)
);

if (isset ($js_files) AND is_array($js_files)) {
    $js_files = array_merge($admin_js_files, $js_files);
} else {
    $js_files = $admin_js_files;
}
if (isset ($css_files) AND is_array($css_files)) {
    $css_files = array_merge($admin_css_files, $css_files);
} else {
    $css_files = $admin_css_files;
}

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">

    <title>Admin | <?php echo ( ! empty ($title)) ? $title : kconfig ('system', 'site_name', 'Kwitang NCMS'); ?></title>

    <?php
        // Jika ada, keluarkan css_files
        if ( ! empty ($css_files) AND is_array ($css_files)) {
            foreach ($css_files as $file) {
                echo '    <link rel="stylesheet" type="text/css" href="'.$file.'">'."\n";
            }
        }

        // Jika ada, keluarkan js_files dan script
        if ( ! empty ($js_files) AND is_array ($js_files)) {
            foreach ($js_files as $file) {
                echo '    <script type="text/javascript" src="'.$file.'"></script>'."\n";
            }
        }
    ?>

    <?php if ( ! empty ($script)) echo '<script type="text/javascript">'.$script.'</script>'; ?>
</head>
<body>
    <div id="notify" class="alert" style="display:none"></div>
    <div class="navbar navbar-kwitang navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a class="brand" href="<?php echo site_url ('admin/dashboard/'); ?>"><?php echo word_limiter(kconfig ('system', 'site_name', 'Kwitang CMS'), 3); ?> </a>
                <ul class="nav pull-right">
                    <?php
                        if ($current_user->level === 'ADMIN') {
                            echo '<li><a href="'.site_url ('admin/filemanager').'" class="tips" title="'.lang('k_filemanager').'"><i class="icon-folder-open icon-white"></i></a></li>';
                            echo '<li class="dropdown">'
                                .'  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-wrench icon-white"></i></a>'
                                .'  <ul class="dropdown-menu">'
                                .'      <li><a href="'.site_url ('admin/common').'"><i class="icon-leaf"></i> '.lang('k_common').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/frontend_options').'"><i class="icon-list"></i> '.lang('k_frontend_options').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/frontend_preview').'"><i class="icon-eye-open"></i> '.lang('k_frontend_preview').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/frontend_editor').'"><i class="icon-tint"></i> '.lang('k_frontend_editor').'</a></li>'
                                .'      <li class="divider"></li>'
                                .'      <li><a href="'.site_url ('admin/structure').'"><i class="icon-list-alt"></i> '.lang('k_structure').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/content_type').'"><i class="icon-file"></i> '.lang('k_content_type').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/language').'"><i class="icon-flag"></i> '.lang('k_language').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/menu').'"><i class="icon-random"></i> '.lang('k_menu').'</a></li>'
                                .'      <li class="divider"></li>'
                                .'      <li><a href="'.site_url ('admin/user').'"><i class="icon-user"></i> '.lang('k_users').'</a></li>'
                                .'      <li><a href="'.site_url ('admin/roles').'"><i class="icon-tasks"></i> '.lang('k_privileges').'</a></li>'
                                .'  </ul>'
                                .'</li>'
                                .'<li class="divider"></li>';
                        }
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-user icon-white"></i>
                        </a>
                        <ul class="dropdown-menu" style="width:255px">
                            <?php
                                echo '<li>'
                                    .'  <div style="margin: 8px 12px 8px 24px;">'
                                    .'    <a href="'.site_url ('admin/user_edit/'.$current_user->username).'">'
                                    .'    <i class="icon-edit"></i> '.$current_user->fullname.'</a><br />'
                                    .'    <strong>'.$current_user->username.'</strong><br />'
                                    .'    <small>Last login: '.kdate($current_user->last_login).'</small><br />'
                                    .'  </div>'
                                    .'  <div class="clearfix"></div>'
                                    .'</li>'
                                    .'<li class="divider"></li>'
                                    .'<li>'
                                    .'  <a href="'.site_url ('auth/logout').'"><i class="icon-off"></i> '.lang('k_logout').'</a>'
                                    .'</li>';
                            ?>
                        </ul>
                    </li>
                </ul>
                <div class="nav-collapse collapse">
                <ul class="nav">
                    <?php
                        function __is_selected_structure($structure, $structure_id) {
                            if ($structure->id == $structure_id) {
                                return true;
                            }
                            if ( ! empty($structure->childs)) {
                                foreach ($structure->childs as $value) {
                                    if (__is_selected_structure($value, $structure_id)) {
                                        return true;
                                    }
                                }
                            }

                            return false;
                        }

                        function menuStruktur ($structure, $structure_id = 0, $depth = 0, $active_found = false)
                        {
                            $ret = '';
                            foreach ($structure as $s) {
                                if ( ! priv ('view', $s)) {
                                    continue;
                                }
                                $submenu = '';
                                $exclude_menu =  ($s->in_menu == 0) ? 'exclude_menu' : '';

                                $_this_active = false;
                                if ($depth == 0 AND $active_found == false) {
                                    if (__is_selected_structure($s, $structure_id)) {
                                        $_this_active = true;
                                        $active_found = true;
                                    }
                                }

                                if ($depth < 30) {
                                    // max 30 leaves
                                    if ( ! empty ($s->childs)) {
                                        $submenu = menuStruktur ($s->childs, $structure_id, $depth + 1, $active_found);
                                    }
                                }

                                $s_title = var_lang($s->title);
                                if ($submenu !== '') {
                                    $_classes = ($_this_active?'active ':'');
                                    $_classes.= ($depth == 0) ? 'dropdown' : 'dropdown-submenu';
                                    $ret .='<li class="'.$_classes.'">'
                                          .'<a href="#" class="dropdown-toggle '.$exclude_menu.'" data-toggle="dropdown">'.$s_title.($depth == 0 ? ' <b class="caret"></b>':'').'</a>'
                                          .'<ul class="dropdown-menu">'
                                          .'  <li class="nav-header"><a href="'.site_url ('admin/content/'.$s->id).'">'.$s_title.' </a></li>'
                                          .'  <li class="divider"></li>'
                                          .$submenu
                                          .'</ul>';
                                } else {
                                    $ret .= '<li'.($_this_active?' class="active"':'').'>'
                                           .'<a class="'.$exclude_menu.'" href="'.site_url ('admin/content/'.$s->id).'">'.$s_title.'</a>';
                                }
                                $ret .= '</li>';
                            }

                            return $ret;
                        }
                        $_structure_id = ! empty ($current_structure->id) ? $current_structure->id : 0;
                        echo menuStruktur ($structure_tree, $_structure_id);
                    ?>
                </ul>
                </div><!-- .nav-collapse -->
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="subnav" style="background-color: #f5f5f5">
            <?php
                // Generate Bredcrumb
                echo '<div class="breadcrumb">'
                    .'<a id="helpLink" href="#helpScreen" class="pull-right tips" title="Panduan" data-toggle="modal"><i class="icon-question-sign"></i></a>';
                $separator = ' &raquo; ';
                $jml = count ($breadcrumb);
                $i = 1;
                foreach ($breadcrumb as $key=>$val) {
                    echo (strlen ($key) > 7) ?'<a href="'.$key.'">'.$val.'</a>': $val;

                    if ($val == '[')
                        $separator = ' ';
                    if ($i < $jml)
                        echo $separator;
                    $i++;
                }
                if ($separator == ' ')
                    echo ' ]';
                echo '</div>';

                // pills
                $current_ctid = 0;
                if (isset ($current_sct))
                    $current_ctid = $current_sct->id;
                if ( ! empty ($data_sct) AND count ($data_sct) > 1) {
                    echo '<ul class="nav nav-pills">';
                    foreach ($data_sct as $ct) {
                        $classnya =  ($ct->id === $current_ctid) ? ' class="active"' : '';
                        echo '<li'.$classnya.'><a href="'
                           .site_url ('admin/content/'.$ct->structure_id.'/'. $ct->id).'" title="'.$ct->notes.'">'
                           .var_lang($ct->title).'</a></li>';
                    }
                    echo '</ul>';
                }
            ?>
        </div>
