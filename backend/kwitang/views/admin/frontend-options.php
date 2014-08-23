<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

$js_files = array(asset_url ('ckeditor/ckeditor.js'),
                  asset_url ('elfinder/js/elfinder.min.js'));
$css_files = array(asset_url ('elfinder/css/elfinder.min.css'));

$csrf_protection  = $this->config->item('csrf_protection');
$csrf_token_name  = $this->config->item('csrf_token_name');
$csrf_cookie_name = $this->config->item('csrf_cookie_name');

include 'header.php';

echo form_open('admin/common_update', 'class="form-horizontal" id="form-common"');
echo form_hidden('return_url', current_url());
?>
<div class="container">
    <h1><?php echo lang('k_frontend_options'); ?></h1>

    <div class="row-fluid">
        <div class="span12 well well-small">
            <?php echo lang('k_frontend'); ?>
            <?php echo form_dropdown('frontend', $frontends, kconfig ('system', 'frontend')); ?>
            <input type="submit" value="<?php echo lang('k_save'); ?> &raquo;" class="btn btn-primary pull-right">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
<?php
if (@file_exists ($options_file)) {
    include($options_file);
    echo '  <div class="well text-right">
                <input type="submit" value="'.lang('k_save').' &raquo;" class="btn btn-primary">
            </div>';
} else {
?>
    <div class="page-header">
        <h1>No Options Avaliable</h1>
    </div>
    <p>FrontEnd that You used now, has no option to modify.</p>
<?php
}
?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".editor").each(function () {
            if (typeof $(this).attr("id") == 'undefined') {
                $(this).attr("id", "tmpId" + parseInt(Math.random()*10000).toString());
            }
            CKEDITOR.replace($(this).attr("id"), {
                filebrowserBrowseUrl : "<?php echo site_url ('admin/elfinder'); ?>",
                height: "418"
            });
        });
        $(".simple-editor").each(function () {
            if (typeof $(this).attr("id") == 'undefined') {
                $(this).attr("id", "tmpsId" + parseInt(Math.random()*10000).toString());
            }
            CKEDITOR.replace($(this).attr("id"), {
                toolbar: "Basic",
                filebrowserBrowseUrl : "<?php echo site_url ('admin/elfinder'); ?>",
                height: "120"
            });
        });

        // you must specify "data-target" attribute with id of target callback
        $(".browse").click(function(e) {
            e.preventDefault();
            var target = $(this).attr("data-target");
            $('<div />').dialogelfinder({
                <?php
                echo '"url" : "'.site_url ('admin/elfinder_connector').'",'
                .'"lang" : "en",';
                if ($csrf_protection) {
                    echo '"customData": {"'.$csrf_token_name.'" : getCookie("'.$csrf_cookie_name.'")},';
                }
                ?>
                commandsOptions: {
                    getfile: {
                        oncomplete : 'close'
                    }
                },
                getFileCallback: function( url) {
                    $("#"+target).val(url);
                }
            });
        });
    });
</script>
<?php
echo form_close();
include 'footer.php';
