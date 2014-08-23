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
$lat        = ( isset($content->lat)) ? $content->lat : -2.5;
$lng        = ( isset($content->lng)) ? $content->lng : 112;
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
    <?php if ($can_edit) { ?>
    <div class="span4">
        <div class="inner">
            <div class="well">
                <?php if (priv ('approve')) { ?>
                <div class="pull-left">
                    <strong>Aktif : </strong>
                    <input class="input" type="checkbox" name="active"<?php echo $active == 1 ? ' checked="checked"' : '';?>>
                </div>
                <?php } ?>
                <div class="pull-right">
                    <div class="datetimepicker input-append">
                        <?php echo form_input('pub_date', $pub_date, 'class="input-medium" rel="required|Tanggal terbit"'); ?>
                        <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <p><b>Gambar:</b></p>
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
            <p><b>Keterangan:</b><br>
            <textarea name="description" id="description" class="span12" cols="40" rows="5"><?php echo $description;?></textarea>
            </p>
            <p><i class="icon icon-map"></i>Pilih tempat:</p>
            <input type="hidden" name="lat" id="lat" value="<?php echo $lat; ?>" />
            <input type="hidden" name="lng" id="lng" value="<?php echo $lng; ?>" />
            <div id="map-canvas" style="width:100%;height:300px;"></div>
            <div id="latlng"></div>
            <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
            <script>
              var map;
              var marker;
              var infoWindow;

              function initialize() {
                var curpoint = new google.maps.LatLng(<?php echo $lat.', '.$lng; ?>);
                var mapOptions = {
                  zoom: 4,
                  center: curpoint,
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('map-canvas'),
                                          mapOptions);
                marker = new google.maps.Marker({
                  map: map,
                  position: curpoint
                });
                infoWindow = new google.maps.InfoWindow;
                google.maps.event.addListener(map, 'click', function(event) {
                  document.getElementById("lat").value = event.latLng.lat().toFixed(6);
                  document.getElementById("lng").value = event.latLng.lng().toFixed(6);
                  marker.setPosition(event.latLng);
                  $("#latlng").text(event.latLng.lat().toFixed(6) + ', ' + event.latLng.lng().toFixed(6));
                });
              }

              google.maps.event.addDomListener(window, 'load', initialize);
            </script>
            <p><b>Tags:</b><br>
            <?php echo form_input('tags', $tags, 'id="tags" class="span12 tagManager" placeholder="Tags"'); ?>
            </p>
            <br><br>
        </div>
    </div>
    <div class="span8">
        <div class="inner">
          <textarea name="body" id="body" cols="80" rows="12"><?php echo $body;?></textarea>
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
        filebrowserBrowseUrl : "<?php echo site_url('admin/elfinder'); ?>"
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
