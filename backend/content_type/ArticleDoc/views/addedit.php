<?php
$can_edit = false;
if( ! empty($content)) {
    if (priv ('approve')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/update', 'id="frmArtikel"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv ('posting')) {
        echo form_open_multipart('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/save', 'id="frmArtikel"');
        echo form_hidden('sct_id', $current_sct->id);
        $can_edit = true;
    }
}
echo form_hidden('closethis', 0);

$title      = ( ! empty($content->title)) ? $content->title : '';
$slug       = ( ! empty($content->slug)) ? $content->slug : '';
$pub_date   = ( ! empty($content->pub_date)) ? date('Y-m-d H:i:s', from_gmt($content->pub_date)) : date('Y-m-d H:i:s', from_gmt());
$description= ( ! empty($content->description)) ? $content->description : '';
$foto       = ( ! empty($content->foto)) ? $content->foto : '';
$tags       = ( ! empty($content->tags)) ? $content->tags : '';
$active     = ( isset($content->active)) ? $content->active : 1;
$body       = ( ! empty($content->body)) ? $content->body : '';

$file1      = ( ! empty($content->file1)) ? $content->file1 : '';
$file2      = ( ! empty($content->file2)) ? $content->file2 : '';
$file3      = ( ! empty($content->file3)) ? $content->file3 : '';
$file4      = ( ! empty($content->file4)) ? $content->file4 : '';
$file5      = ( ! empty($content->file5)) ? $content->file5 : '';

?>

<div class="page-header">
    <?php
        if ($can_edit) {
            $tmp = empty($content->id) ? 'Tambah' : 'Ubah';
        } else {
            $tmp = 'Tampil';
        }
        echo '<h1>'.$tmp.' '.var_lang($current_sct->title).'</h1>';
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
        <div class="inner">

            <?php if (priv ('approve')) { ?>
            <h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>:</h5>
            <div>
                <div class="pull-left">
                    <input class="input" type="checkbox" name="active"<?php echo $active == 1 ? ' checked="checked"' : '';?>> Aktif
                </div>
                <div class="pull-right">
                    <div class="datetimepicker input-append">
                        <?php echo form_input('pub_date', $pub_date, 'class="input-medium" rel="required|Tanggal terbit"'); ?>
                        <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php } ?>

            <h5 class="detail-title">Foto <small><em>(gambar)</em></small>:</h5>
            <?php
                if( ! empty($foto) AND @file_exists($content->foto)) {
                    echo '<p class="pull-right"><a href="'.site_url('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/delg/'.$content->id ).'" style="color: #f33" class="askdelete" title="Gambar Artikel">Hapus Gambar</a></p>';
                    echo '<a href="#myModal" role="button" class="img-polaroid pull-left" data-toggle="modal"><img src="'.base_url($content->thumbnail).'" alt="Thumbnail"></a>';
                    echo '<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                              <h3 id="myModalLabel">Gambar</h3>
                            </div>
                            <div class="modal-body">
                              <p><img src="'.base_url($content->foto).'" alt="" title="" /></p>
                            </div>
                            <div class="modal-footer">
                              <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
                            </div>
                          </div>
                          <div class="clearfix"></div>';
                }
            ?>
            <input type="file" name="foto">

            <h5 class="detail-title">Deskripsi <small><em>(excerpt/lead)</em></small>:</h5>
            <textarea name="description" id="description" class="span12" cols="40" rows="5"><?php echo $description;?></textarea>

            <h5 class="detail-title">Tags <small><em>(keywords)</em></small>:</h5>
            <?php echo form_input('tags', $tags, 'id="tags" class="span12 tagManager tips" placeholder="Tags/keywords" title="Pisahkan dengan koma"'); ?>

            <h5 class="detail-title">Files <small><em>(attachment)</em></small>:</h5>
            <?php
              for ($i=1;$i<=5;$i++) {
                $file = 'file'.$i;
                $file = $$file;
                if ( ! empty ($content) AND ! empty ($file)) {
                  echo '  <span style="float:right"><a href="'.site_url('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/del_file/'.$content->id.'/'.$i).'" style="color: #f33" class="askdelete" title="File '.$i.': '.rawurlencode(basename($file)).'">Hapus</a></span>';
                }
                echo '  <div style="border-bottom: 1px dotted #d4d4d4">File '.$i.': <a href="'.base_url($file).'" target="_blank">'.basename($file).'</a><br>'
                    .form_upload('file'.$i).'</div>';
              }
            ?>
        </div>
    </div>
    <div class="span8">
        <div class="inner">
          <textarea name="body" id="body" cols="80" rows="12"><?php echo $body;?></textarea>
        </div>
        <hr>
        <div class="well">
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
        <div>
            <div style="border: 1px solid #b6b6b6; padding: 10px">
                <?php echo $body; ?>
            </div>
            <hr />
            <div>
              <h2>Files</h2>
              <?php
                for ($i=1;$i<=5;$i++) {
                  $file = 'file'.$i;
                  $file = $$file;
                  echo '<p style="border-bottom: 1px dotted #d4d4d4">File 1: <a href="'.base_url($file).'" target="_blank">'.basename($file).'</a></p>';
                }
              ?>
            </div>
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

   function simpan_tutup()
   {
      var keys = '';
      $('.myTag').each(function () {
        var a = $(this).find('span').text().trim();
        keys += ','+a;
      });
      $("#tags").val(keys.substr(1,keys.length));
      $("input[name=closethis]").val('1');
      $('#frmArtikel').submit();
   }

   function title_case()
   {
       var txt = $("#title").val();
       txt = txt.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
       $("#title").val(txt);
   }
<?php } ?>

   function tutup()
   {
      // TODO: if not changed, else warn
      location.href= '<?php echo $return_url; ?>';
   }
</script>
