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
echo form_hidden('closethis', 0);

$title      = ( ! empty($content->title)) ? $content->title : '';
$slug       = ( ! empty($content->slug)) ? $content->slug : '';
$pub_date   = ( ! empty($content->pub_date)) ? date('Y-m-d H:i:s', from_gmt($content->pub_date)) : date('Y-m-d H:i:s', from_gmt());
$description= ( ! empty($content->description)) ? $content->description : '';
$foto       = ( ! empty($content->foto)) ? $content->foto : '';
$tags       = ( ! empty($content->tags)) ? $content->tags : '';
$active     = ( isset($content->active)) ? $content->active : 1;
$body       = ( ! empty($content->body)) ? $content->body : '';

$date_start   = ( ! empty($content->date_start)) ? date('Y-m-d', from_gmt($content->date_start)) : date('Y-m-d', from_gmt());
$date_end   = ( ! empty($content->date_end)) ? date('Y-m-d', from_gmt($content->date_end)) : date('Y-m-d', from_gmt());
$time       = ( ! empty($content->time)) ? $content->time : '';
$venue      = ( ! empty($content->venue)) ? $content->venue : '';
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
<div class="row-fluid">
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
            <div class="thumbnail">
                <div class="caption">
                <h5 class="detail-title">Tanggal Agenda: </h5>
                <div id="date_start" class="input-append tips" title="Tanggal Awal Agenda">
                    <?php echo form_input('date_start', $date_start, 'class="input-small" data-required="Tanggal Mulai"'); ?>
                    <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                </div>
                -
                <div id="date_end" class="input-append tips" title="Tanggal Akhir Agenda">
                    <?php echo form_input('date_end', $date_end, 'class="input-small" data-required="Tanggal Selesai"'); ?>
                    <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                </div>
                <div class="clearfix"></div>

                <h5 class="detail-title">Waktu : </h5>
                <?php echo form_input('time', $time, 'placeholder="08:00 - 12:00"'); ?>

                <h5 class="detail-title">Tempat/Venue : </h5>
                <?php echo form_input('venue', $venue, 'placeholder="Nama Venue"'); ?>
                </div>
            </div>

            <?php if (priv ('approve')) { ?>
            <div>
                <h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>: </h5>
                <div class="pull-left">
                    <label class="checkbox pull-left" style="margin:6px 24px 0 0;">
                        <input class="input" type="checkbox" name="active"<?php echo $active == 1 ? ' checked="checked"' : '';?>> Aktif
                    </label>
                </div>
                <div class="pull-right">
                    <div class="datetimepicker input-append tips" title="Tanggal dipublikasikan">
                        <?php echo form_input('pub_date', $pub_date, 'class="input-medium" rel="required|Tanggal terbit"'); ?>
                        <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php } ?>

            <h5 class="detail-title">Foto <small><em>(gambar)</em></small>:</h5>
            <input type="file" name="foto">
            <?php
                if( ! empty($foto) AND @file_exists($content->foto)) {
                    echo '<p><a href="' . site_url('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/delg/' . $content->id ) . '" style="color: #f33">Hapus Gambar</a></p>';
                    echo '<a href="#myModal" role="button" class=" pull-right" data-toggle="modal"><img src="' . base_url($content->thumbnail) . '" alt="Thumbnail"></a>';
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
                          </div>';
                }
            ?>
            <div class="clearfix"></div>

            <h5 class="detail-title">Deskripsi <small><em>(excerpt/lead)</em></small>:</h5>
            <textarea name="description" id="description" class="span12" cols="40" rows="5"><?php echo $description;?></textarea>

            <h5 class="detail-title">Tags <small><em>(keywords)</em></small>:</h5>
            <?php echo form_input('tags', $tags, 'id="tags" class="span12 tagManager tips" placeholder="Tags/keywords" title="Pisahkan dengan koma"'); ?>
        </div>
    </div>
    <div class="span8">
        <div class="inner">
          <textarea name="body" id="body"><?php echo $body;?></textarea>
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
            <br />
            <?php
            echo '<div style="align:center">';
            if ($date_start != $date_end) {
                echo '<p>Tanggal: '.$date_start.' s/d '.$date_end.'</p>';
            } else {
                echo '<p>Tanggal: '.$date_start.'</p>';
            }
            echo '<p>Waktu: '.$time.'</p>'
                .'<p>Tempat/Venue: '.$venue.'</p>'
                .'</div><hr />';
            echo '<div style="border: 1px solid #b6b6b6; padding: 10px">'.$body.'</div>';
            ?>
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
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin;
    var checkout;

    $(document).ready(function() {

      checkin = $('#date_start').datetimepicker({
        format: 'yyyy-MM-dd',
        pickTime: false,
        startDate: now // still not working as expected
      }).on('changeDate', function(ev) {
        checkin.datetimepicker.hide();
        if (ev.date.valueOf() > checkout.datetimepicker._date.valueOf()) {
          var newDate = new Date(ev.date)
          newDate.setDate(newDate.getDate());
          checkout.datetimepicker.setValue(newDate);
          $('#date_end').find('.add-on').trigger('click');
        }
      }).data();

      checkout = $('#date_end').datetimepicker({
        format: 'yyyy-MM-dd',
        pickTime: false
      }).on('changeDate', function(ev) {
        checkout.datetimepicker.hide();
        if (ev.date.valueOf() < checkin.datetimepicker._date.valueOf()) {
          var newDate = new Date(ev.date)
          newDate.setDate(newDate.getDate());
          checkin.datetimepicker.setValue(newDate);
        }
      }).data();

      var tmp = $("#tags").val().split(',');
      $("#tags").tagsManager().val('');
      for(var i=0;i<tmp.length;i++) {
        $(".tagManager").tagsManager('pushTag',tmp[i]);
      }

      CKEDITOR.replace( 'body', {
        filebrowserBrowseUrl : "<?php echo site_url('admin/elfinder'); ?>",
        height: "320"
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
