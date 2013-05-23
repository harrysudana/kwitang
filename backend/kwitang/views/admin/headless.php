<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

$admin_css_files = array(
    asset_url ('css/jquery-ui-1.8.23.custom.css', false),
    asset_url ('css/bootstrap.min.css', false),
    asset_url ('css/bootstrap-datetimepicker.min.css', false),
    asset_url ('css/admin.css', false)
);

$admin_js_files = array(
    asset_url ('js/jquery-1.7.2.min.js', false),
    asset_url ('js/modernizr-2.6.1-respond-1.1.0.min.js', false),
    asset_url ('js/jquery-ui-1.8.23.custom.min.js', false),
    asset_url ('js/bootstrap.min.js', false),
    asset_url ('js/bootstrap-datetimepicker.min.js', false),
    asset_url ('dtables/js/jquery.dataTables.min.js', false),
    asset_url ('js/dtablebootstrap.js', false),
    asset_url ('js/admin.js', false)
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

    <title>Admin | <?php echo ( ! empty ($title)) ? $title : kconfig ('system', 'site_name', 'CyberCMS'); ?></title>

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
    <style type="text/css">
        body {background-image: none;}
    </style>
    <script type="text/javascript">
        var UPLOAD_FOLDER = "<?php echo kconfig ('system', 'upload_folder', 'upload/'); ?>";
        var BASE_URL = "<?php echo base_url (); ?>";
    </script>
    <?php if ( ! empty ($script)) echo '<script type="text/javascript">'.$script.'</script>'; ?>
</head>
<body>
    <div id="notify" class="alert" style="display:none"></div>

    <div class="container">
        <div class="navbar subnav cybersubnav">
            <?php
                $current_ctid = 0;
                if (isset ($current_sct))
                    $current_ctid = $current_sct->id;
                if ( ! empty ($data_sct) AND count ($data_sct) > 1) {
                    echo '<ul class="nav nav-pills">';
                    foreach ($data_sct as $ct) {
                        $classnya =  ($ct->id === $current_ctid) ? ' class="active"' : '';
                        echo '<li'.$classnya.'><a href="'
                           .site_url ('admin/content/'.$ct->structure_id.'/'. $ct->id).'" title="'.var_lang($ct->notes).'">'
                           .var_lang($ct->title).'</a></li>';
                    }
                    echo '</ul>';
                }
            ?>
            <div class="clearfix"></div>
        </div>
