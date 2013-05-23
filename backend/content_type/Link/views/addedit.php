<?php
$can_edit = false;
if( ! empty($content)) {
    if (priv ('approve')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/update', 'id="frmLink"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv ('posting')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/save', 'id="frmLink"');
        $can_edit = true;
    }
}
echo form_hidden('closethis', 0);

$title      = ( ! empty($content->title)) ? $content->title : '';
$url        = ( ! empty($content->url)) ? $content->url : '';
$image      = ( ! empty($content->image)) ? $content->image : '';
$active     = ( isset($content->active)) ? $content->active : 1;

?>
<div class="page-header">
    <?php
        if ($can_edit) {
            $tmp = empty($content->id) ? 'Tambah' : 'Ubah';
        } else {
            $tmp = 'Tampil';
        }
        echo  '<h1>'.$tmp.' '.var_lang($current_sct->title).'</h1>';
    ?>
</div>
<div class="row-fluid subnav">
    <div class="cmdbar">
        <div class="span8">
            <?php
            if ($can_edit) {
                echo '<div class="btn-group span12">'
                    .form_input('title', $title, 'class="form_title span11" id="title" data-required="Judul" placeholder="Judul" size="80" maxlength="120"')
                    .'<input type="button" onclick="title_case()" class="btn tips btn-mini" value=" A/a " title="Format teks menjadi besar kecil">'
                    .'</div>';
            } else {
                echo '<div class="pull-left" style="margin-top: 6px;">'.$title.'</div>';
            }
            ?>
        </div>
        <div class="span4">
            <div class="btn-group pull-right">
                <?php if ($can_edit) { ?>
                <a href="javascript:simpan()" class="btn">
                    Simpan
                </a>
                <a href="javascript:simpan_tutup()" class="btn btn-primary">
                    Simpan &amp; Tutup
                </a>
                <?php } ?>
                <a href="javascript:tutup()" class="btn btn-inverse">
                    Tutup
                </a>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="row-fluid" style="margin-top: 10px;">
    <div class="span12 form-horizontal">
        <?php
        if (priv ('approve')) {
            echo '<div class="control-group">
                    <label class="control-label" for="aktif">Aktif</label>
                    <div class="controls">
                        <input id="aktif" type="checkbox" name="active"'.($active == 1 ? ' checked="checked"' : '').' >
                    </div>
                </div>';
        }

        if ($can_edit) {
            echo '<div class="control-group">
                    <label class="control-label" for="url">URL</label>
                    <div class="controls">
                        '.form_input('url', $url, 'id="url" class="input-xxlarge"').'
                    </div>
                </div>';
            echo '<div class="control-group">
                    <label class="control-label" for="image">Gambar</label>
                    <div class="controls">
                        '.form_upload('image', $image, 'id="image" class="input-xlarge"').'
                    </div>
                </div>';
            if ( ! empty($image)) {
                echo '<p><img src="'.base_url($image).'" alt=""></p>';
            }

        } else {
            echo '<p>Aktif: '. (($active == 1) ? 'Yes' : 'No').'</p>';
            echo '<p>URL: '.$url.'</p>';
            if ( ! empty($image)) {
                echo '<p><img src="'.base_url($image).'" alt=""></p>';
            }
        }
        ?>


    </div>
</div>
<?php
if ($can_edit) {
    echo form_close();
}
?>
<script type="text/javascript">
<?php
if ($can_edit) {
?>
    $(document).ready(function() {
    });

    function simpan() {
        $('#frmLink').submit();
    }

    function simpan_tutup() {
        $("input[name=closethis]").val('1');
        $('#frmLink').submit();
    }

    function title_case() {
        var txt = $("#title").val();
        txt = txt.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        $("#title").val(txt);
    }
<?php
}
?>
    function tutup() {
        location.href= '<?php echo $return_url; ?>';
    }
</script>
