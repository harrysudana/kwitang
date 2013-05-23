<?php
$can_edit = false;
if( ! empty($content)) {
    if (priv ('approve')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/update', 'id="frmArtikel"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv ('posting')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/save', 'id="frmArtikel"');
        echo form_hidden('sct_id', $current_sct->id);
        $can_edit = true;
    }
}

if ($can_edit) {
  echo form_hidden('closethis', 0);
}

$title      = ( ! empty($content->title)) ? $content->title : '';
$slug       = ( ! empty($content->slug)) ? $content->slug : '';
$pub_date   = ( ! empty($content->pub_date)) ? date('Y-m-d H:i:s', from_gmt($content->pub_date)) : date('Y-m-d H:i:s', from_gmt());
$description= ( ! empty($content->description)) ? $content->description : '';
$foto       = ( ! empty($content->foto)) ? $content->foto : '';
$tags       = ( ! empty($content->tags)) ? $content->tags : '';
$active     = ( isset($content->active)) ? $content->active : 1;
$body       = ( ! empty($content->body)) ? $content->body : '';
?>

<div class="page-header">
    <?php
        if ($can_edit) {
            $tmp = empty($content->id) ? 'Tambah' : 'Ubah';
        } else {
            $tmp = 'Tampil';
        }
        echo '<h1>'.$tmp.' ' .var_lang($current_sct->title). '</h1>';
    ?>
</div>
<div class="row-fluid subnav">
    <div class="cmdbar">
        <div class="span12">
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
        <div class="clearfix"></div>
    </div>
</div>
<div class="row-fluid" style="margin-top: 10px;">
    <?php if ($can_edit) { ?>
    <div class="span4">
        <?php if (priv ('approve')) { ?>
        <h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>:</h5>
        <div>
            <label class="pull-left checkbox" style="margin-top: 6px;">
                <input type="checkbox" name="active"<?php echo $active == 1 ? ' checked="checked"' : '';?>> Aktif
            </label>
            <div class="pull-right datetimepicker input-append">
                <?php echo form_input('pub_date', $pub_date, 'class="input-medium tips" rel="required|Tanggal terbit" title="Tanggal Tayang"'); ?>
                <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php } ?>

        <h5 class="detail-title">Deskripsi <small><em>(excerpt/lead)</em></small>:</h5>
        <textarea name="description" id="description" class="span12" cols="40" rows="5"><?php echo $description;?></textarea>

        <h5 class="detail-title">Tags <small><em>(keywords)</em></small>:</h5>
        <?php echo form_input('tags', $tags, 'id="tags" class="span12 tagManager tips" placeholder="Tags/keywords" title="Pisahkan dengan koma"'); ?>
        </p>
    </div>
    <div class="span8">
        <div class="inner">
          <textarea name="body" id="body" cols="80" rows="12"><?php echo $body;?></textarea>
          <hr>
          <?php if ($can_edit) { ?>
          <div class="well">
              <div class="btn-group pull-right">
                  <a href="javascript:simpan()" class="btn btn-primary"> Simpan </a>
              </div>
              <div class="clearfix"></div>
          </div>
          <?php } ?>
        </div>
    </div>
    <?php } else { ?>
    <div class="span4">
        <?php
            echo '<p><b>Active:</b> <i class="icon '.($active ? 'icon-green icon-ok':'icon-red icon-remove').'"></i></p>';
            echo '<p><b>Publish:</b> '.$pub_date.'</p>';
            echo '<p><b>Gambar:</b> '.$content->foto.'</p>';
            echo '<p><b>Keterangan:</b> '.$description.'</p>';
            echo '<p><b>Tags:</b> '.$tags.'</p>';
        ?>
    </div>
    <div class="span8">
        <div style="border: 1px solid #b6b6b6; padding: 10px">
            <?php echo $body; ?>
        </div>
    </div>
    <?php } ?>
</div>
<?php
if ($can_edit) {
    echo form_close();
}
?>

<script type="text/javascript">
<?php if ($can_edit) { ?>
    $(document).ready(function() {
      var tmp = $("#tags").val().split(',');
      $("#tags").tagsManager().val('');
      for(var i=0;i<tmp.length;i++) {
        $(".tagManager").tagsManager('pushTag',tmp[i]);
      }

      CKEDITOR.replace( 'body', {
        filebrowserBrowseUrl : "<?php echo site_url('admin/elfinder'); ?>",
        height: "418"
      });
    });

   function simpan()
   {
      var keys = '';
      $('.myTag').each(function () {
        var a = $(this).find('span').text().trim();
        keys += ','+a;
      });
      $("#tags").val(keys.substr(1,keys.length));
      $('#frmArtikel').submit();
   }

   function title_case()
   {
       var txt = $("#title").val();
       txt = txt.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
       $("#title").val(txt);
   }
<?php } ?>
</script>
