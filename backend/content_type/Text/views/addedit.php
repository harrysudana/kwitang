<?php
$can_edit = false;
if( ! empty($content)) {
    if (priv ('approve')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/update', 'id="frmText"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv ('posting')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/save', 'id="frmText"');
        $can_edit = true;
    }
}

if ($can_edit) {
    echo form_hidden('closethis', 0);
}

$title      = ( ! empty($content->title)) ? $content->title : '';
$body       = ( ! empty($content->body)) ? $content->body : '';
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
<div class="row-fluid">
    <div class="span12">
        <div class="cmdbar">
            <?php
                if ($can_edit) {
                    echo '<div class="btn-group span12">'
                        .form_input('title', $title, 'class="form_title span11" id="title" data-required="Judul" placeholder="Judul" size="80" maxlength="120"')
                        .'<input type="button" onclick="title_case()" class="btn tips btn-mini" value=" A/a " title="Format teks menjadi besar kecil">'
                        .'</div>';
                } else {
                    echo '<p>'.$title.'</p>';
                }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
        if (priv ('approve')) {
            echo '  <h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>:</h5>
                    <label class="checkbox">
                        <input type="checkbox" name="active"'.($active == 1 ? ' checked="checked"' : '').' > Aktif
                    </label>
                    <hr>';
        }

        if ($can_edit) {
            echo '<textarea name="body" id="bodyWow">'.($body).'</textarea>';
        } else {
            echo '<div style="border: 1px solid #b6b6b6; padding: 10px">'.($body).'</div>';
        }
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <hr>
        <div class="well">
            <div class="btn-group pull-right">
                <?php if ($can_edit) { ?>
                    <a href="javascript:simpan()" class="btn"> Simpan </a>
                    <a href="javascript:simpan_tutup()" class="btn btn-primary"> Simpan &amp; Tutup </a>
                <?php } ?>
                <a href="javascript:tutup()" class="btn btn-inverse"> Tutup </a>
            </div>
        </div>
    </div>
</div>

<?php
if ($can_edit) {
    echo form_close();
}
?>
<script type="text/javascript">
    <?php if ($can_edit) {?>

    var oEditor;
    $(document).ready(function() {
        oEditor = CKEDITOR.replace( 'bodyWow', {
            filebrowserBrowseUrl : "<?php echo site_url('admin/elfinder'); ?>",
            height: "418"
        });

        var tags = $("#tags").val();
        if (tags) {
            var tmp = tags.split(',');
            $("#tags").tagsManager().val('');

            for(var i=0;i<tmp.length;i++) {
                $(".tagManager").tagsManager('pushTag',tmp[i]);
            }
        }
    });

    function pre_simpan() {
        // tags
        var keys = '';
        $('.myTag').each(function () {
        var a = $(this).find('span').text().trim();
            keys += ','+a;
        });
        $("#tags").val(keys.substr(1,keys.length));

        // fix messy CKEditor
        //var raw = $(oEditor.element.$).html();
        //var cleaned = $("<div/>").html(raw).text();
        //oEditor.setData(cleaned);
    }

    function simpan() {
        pre_simpan();
        $('#frmText').submit();
    }

    function simpan_tutup() {
        pre_simpan();
        $("input[name=closethis]").val('1');
        $('#frmText').submit();
    }

    function title_case() {
        var txt = $("#title").val();
        txt = txt.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        $("#title").val(txt);
    }
    <?php } ?>

    function tutup() {
        location.href= '<?php echo $return_url; ?>';
    }
</script>
