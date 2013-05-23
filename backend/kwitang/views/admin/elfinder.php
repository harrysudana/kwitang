<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!'); ?>
<html>
<head>
  <title>Browse Server</title>

  <link rel="stylesheet" href="<?php echo asset_url ('css/jquery-ui-1.8.23.custom.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url ('elfinder/css/elfinder.min.css'); ?>">
</head>
<body>
  <div id="elfinder"></div>
<script type="text/javascript" src="<?php echo asset_url ('js/jquery-1.7.2.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset_url ('js/jquery-ui-1.8.23.custom.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset_url ('elfinder/js/elfinder.min.js'); ?>"></script>
<script type="text/javascript" charset="utf-8">
    // Helper function to get parameters from the query string.
    function getUrlParam(paramName)
    {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : '';
    }

    function getCookie(c_name) {
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");

    if (c_start == -1) {
      c_start = c_value.indexOf(c_name + "=");
    }

    if (c_start == -1) {
        c_value = null;
    } else {
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);

        if (c_end == -1) {
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }

    return c_value;
}

    $().ready(function() {
        var funcNum = getUrlParam('CKEditorFuncNum');

        var elf = $('#elfinder').elfinder({
            <?php
                $csrf_protection = $this->config->item('csrf_protection');
                $csrf_token_name = $this->config->item('csrf_token_name');
                $csrf_cookie_name = $this->config->item('csrf_cookie_name');

                echo '"url" : "'.site_url ('admin/elfinder_connector').'",'
                    .'"lang" : "en",';
                if ($csrf_protection) {
                    echo '"customData": {"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'")},';
                }
            ?>
            getFileCallback : function(file) {
                window.opener.CKEDITOR.tools.callFunction(funcNum, file);
                window.close();
            },
            resizable: false
        }).elfinder('instance');

        $(window).resize(function() {
          $('#elfinder').height($(window).height() - $('#elfinder .el-finder-toolbar').height() - $('#elfinder .el-finder-statusbar').height() - 6 - 20);
        });
    });
</script>
</body>
</html>
