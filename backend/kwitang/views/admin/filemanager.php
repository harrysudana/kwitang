<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

$js_files  = array(asset_url ('elfinder/js/elfinder.min.js'));
$css_files = array(asset_url ('elfinder/css/elfinder.min.css'));

$csrf_protection = $this->config->item('csrf_protection');
$csrf_token_name = $this->config->item('csrf_token_name');
$csrf_cookie_name = $this->config->item('csrf_cookie_name');

include 'header.php';
?>
<div style="margin: 18px;">
    <div id="filemanager"></div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('#filemanager').elfinder({
            <?php
                echo '"url" : "'.site_url ('admin/elfinder_connector').'",'
                    .'"lang" : "en"';
                if ($csrf_protection) {
                    echo ',"customData": {"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'")}';
                }
            ?>
        });
    });
</script>

<?php
include 'footer.php';
