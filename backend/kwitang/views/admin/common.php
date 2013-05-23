<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

$js_files = array(asset_url ('ckeditor/ckeditor.js'),
                  asset_url ('elfinder/js/elfinder.min.js'));
$css_files = array(asset_url ('elfinder/css/elfinder.min.css'));

$csrf_protection  = $this->config->item('csrf_protection');
$csrf_token_name  = $this->config->item('csrf_token_name');
$csrf_cookie_name = $this->config->item('csrf_cookie_name');

include 'header.php';

echo form_open('admin/common_update', 'class="form-horizontal" id="form-common"');
?>
<div class="row-fluid">
    <div class="span12">
        <div class="md-master">
            <h2><?php echo lang('k_setting').' '.lang('k_common'); ?></h2>
            <hr>
            <ul class="nav nav-list">
                <li><a href="#website"><?php echo lang('k_website'); ?> <i class="pull-right icon-chevron-right"></i></a></li>
                <li><a href="#imageupset"><?php echo lang('k_image_setting'); ?> <i class="pull-right icon-chevron-right"></i></a></li>
                <li><a href="#videoupset"><?php echo lang('k_video_setting'); ?> <i class="pull-right icon-chevron-right"></i></a></li>
                <li><a href="#datetz"><?php echo lang('k_date_tz'); ?> <i class="pull-right icon-chevron-right"></i></a></li>
                <li><a href="#adminset"><?php echo lang('k_admin_setting'); ?> <i class="pull-right icon-chevron-right"></i></a></li>
            </ul>
            <br>
            <p class="txt-center">
                <input type="submit" value=" <?php echo lang('k_save'); ?> &raquo;" class="btn btn-primary btn-large">
            </p>
        </div>
        <div class="md-detail">
            <div class="md-item" id="website">
                <div class="page-header">
                    <h3><?php echo lang('k_website'); ?></h3>
                </div>
                <div class="control-group">
                    <label class="control-label" for="site_name"><?php echo lang('k_site_name'); ?></label>
                    <div class="controls">
                        <?php echo form_input('site_name', kconfig ('system', 'site_name'), 'id="site_name" data-placement="left" class="tips input-xlarge" title="site_name"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="site_slogan"><?php echo lang('k_site_slogan'); ?></label>
                    <div class="controls">
                        <?php echo form_input('site_slogan', kconfig ('system', 'site_slogan'), 'id="site_slogan" data-placement="left" class="tips input-xlarge" title="site_slogan"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="site_keywords"><?php echo lang('k_site_keywords'); ?></label>
                    <div class="controls">
                        <?php echo form_input('site_keywords', kconfig ('system', 'site_keywords'), 'id="site_keywords" data-placement="left" class="tips input-xlarge" title="site_keywords"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="site_logo">Logo URL</label>
                    <div class="controls">
                        <div class="input-append">
                            <?php echo form_input('site_logo', kconfig ('system', 'site_logo'), 'id="site_logo" data-placement="left" class="tips" title="site_logo"'); ?>
                            <button class="btn" type="button" id="openlogo"><?php echo lang('k_select'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="site_favicon">Favicon URL</label>
                    <div class="controls">
                        <div class="input-append">
                            <?php echo form_input('site_favicon', kconfig ('system', 'site_favicon'), 'id="site_favicon" data-placement="left" class="tips" title="site_favicon"'); ?>
                            <button class="btn" type="button" id="openfavicon"><?php echo lang('k_select'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="item_perpage"><?php echo lang('k_item_per_page'); ?></label>
                    <div class="controls">
                        <?php echo form_input('item_perpage', kconfig ('system', 'item_perpage', 10), 'id="item_perpage" data-placement="left" class="tips span1" title="item_perpage"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="enable_stat"><?php echo lang('k_enable_stat'); ?></label>
                    <div class="controls">
                        <?php
                        echo form_radio('enable_stat', '1', (kconfig ('system', 'enable_stat', 0) ==1?true:false)).' <span>'.lang('k_yes').'</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        echo form_radio('enable_stat', '0', (kconfig ('system', 'enable_stat', 0) ==0?true:false)).' <span>'.lang('k_no').'</span>';
                        ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_cache_lifetime'); ?></label>
                    <div class="controls">
                        <?php
                            $cache_lifetime = kconfig ('system', 'cache_lifetime', 0);
                            $cache_option = array('0' => lang('k_cache_disabled'));
                            for ($i=1;$i<16;$i++) {
                                $cache_option[$i] = $i.' '.lang('k_minutes');
                            }
                            echo form_dropdown('cache_lifetime', $cache_option, $cache_lifetime);
                        ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="item_perpage"><?php echo lang('k_tracking_code'); ?></label>
                    <div class="controls">
                        <?php echo form_textarea('tracking_code', kconfig ('system', 'tracking_code'), 'id="tracking_code" data-placement="left" class="tips span8" style="height:100px" title="tracking_code"'); ?>
                    </div>
                </div>
            </div>

            <div class="md-item" id="imageupset">
                <div class="page-header">
                    <h3><?php echo lang('k_image_setting'); ?></h3>
                </div>

                <div class="control-group">
                    <label class="control-label" for="allowed_types"><?php echo lang('k_allowed_types'); ?></label>
                    <div class="controls">
                        <?php echo form_input('allowed_types', kconfig ('system', 'allowed_types', 'jpg|jpeg|png|gif'), 'data-placement="left" class="tips" title="allowed_types"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="image_max_size"><?php echo lang('k_image_max_size'); ?></label>
                    <div class="controls">
                        <?php echo form_input('image_max_size', kconfig ('system', 'image_max_size', '1024'), 'data-placement="left" class="tips" title="image_max_size"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="image_max_width"><?php echo lang('k_image_max_width'); ?></label>
                    <div class="controls">
                        <?php echo form_input('image_max_width', kconfig ('system', 'image_max_width', '1000'), 'data-placement="left" class="tips" title="image_max_width"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="image_max_height"><?php echo lang('k_image_max_height'); ?></label>
                    <div class="controls">
                        <?php echo form_input('image_max_height', kconfig ('system', 'image_max_height', '800'), 'data-placement="left" class="tips" title="image_max_height"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_thumbnail_width'); ?></label>
                    <div class="controls">
                        <?php echo form_input('thumbnail_width', kconfig ('system', 'thumbnail_width', '150'), 'data-placement="left" class="tips" title="thumbnail_width"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_thumbnail_height'); ?></label>
                    <div class="controls">
                        <?php echo form_input('thumbnail_height', kconfig ('system', 'thumbnail_height', '150'), 'data-placement="left" class="tips" title="thumbnail_height"'); ?>
                    </div>
                </div>
            </div>

            <div class="md-item" id="videoupset">
                <div class="page-header">
                    <h3><?php echo lang('k_video_setting'); ?></h3>
                </div>

                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_image_max_size'); ?></label>
                    <div class="controls">
                        <?php echo form_input('video_max_size', kconfig ('system', 'video_max_size', '2048'), 'data-placement="left" class="tips" title="video_max_size"'); ?>
                    </div>
                </div>
            </div>

            <div class="md-item" id="datetz">
                <div class="page-header">
                    <h3><?php echo lang('k_date_tz'); ?></h3>
                </div>

                <div class="control-group">
                    <label class="control-label" for="timezones"><?php echo lang('k_timezone'); ?></label>
                    <div class="controls">
                        <?php echo timezone_menu(kconfig ('system', 'timezones', 'UP7'), 'tips input-xxlarge'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="dst"><?php echo lang('k_dst'); ?></label>
                    <div class="controls">
                        <?php
                        echo form_radio('dst', '0', (kconfig ('system', 'dst', '0') == '0'?true:false)).' '.lang('k_no').' &nbsp;&nbsp;&nbsp;&nbsp;';
                        echo form_radio('dst', '1', (kconfig ('system', 'dst', '0') == '1'?true:false)).' '.lang('k_yes');
                        ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="date_format"><?php echo lang('k_date_format'); ?></label>
                    <div class="controls">
                        <?php echo form_input('date_format', kconfig ('system', 'date_format', 'j M Y H:i a'), 'data-placement="left" class="tips input-small" title="date_format"'); ?>
                    </div>
                </div>
            </div>

            <div class="md-item" id="adminset">
                <div class="page-header">
                    <h3><?php echo lang('k_admin_setting'); ?></h3>
                </div>

                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_dashboard_text'); ?></label>
                    <div class="controls">
                        <?php echo form_textarea('dashboard_text', kconfig ('system', 'dashboard_text', '<h1>Selamat datang...</h1>'), 'id="dashboard_text"'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label"><?php echo lang('k_admin_footer'); ?></label>
                    <div class="controls">
                        <?php echo form_input('admin_footer_text', kconfig ('system', 'admin_footer_text'), 'class="input-xxlarge"'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    echo form_close();
?>

<script type="text/javascript">
    var dialog;

    $(document).ready(function() {
        $("body").scrollspy();

        CKEDITOR.replace('dashboard_text', {
            filebrowserBrowseUrl : "<?php echo site_url ('admin/elfinder'); ?>",
            height: "418"
        });

        $("#openlogo").click(function() {
            var dialog2 = $('<div />').dialogelfinder({
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
                    $("#site_logo").val(url);
                }
            });
        });

        $("#openfavicon").click(function() {
            var dialog2 = $('<div />').dialogelfinder({
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
                    $("#site_favicon").val(url);
                }
            });
        });

        $(".input-link").bind("change paste keyup", function() {
            if ($(this).val().substr(0,4) != 'http') {
                $(this).val("http://"+$(this).val());
            }
        });

        $("#form-common").submit(function(e) {
            // cleanup before submit
            $(".input-link").each(function() {
                if ($(this).val() == "http://") {
                    $(this).val("");
                }
            });
        });
    });
</script>

<?php
include 'footer.php';
