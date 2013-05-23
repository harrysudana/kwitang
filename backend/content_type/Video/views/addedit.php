<?php
$title      = ( ! empty($content->title)) ? $content->title : '';
$pub_date   = ( ! empty($content->pub_date)) ? date('Y-m-d H:i:s', from_gmt($content->pub_date)) : date('Y-m-d H:i:s', from_gmt());
$description= ( ! empty($content->description)) ? $content->description : '';
$tags       = ( ! empty($content->tags)) ? $content->tags : '';
$image      = ( ! empty($content->image)) ? $content->image : '';
$video_file = ( ! empty($content->video_file)) ? $content->video_file : '';
$youtube_id = ( ! empty($content->youtube_id)) ? $content->youtube_id : '';
$active     = ( isset($content->active)) ? $content->active : 1;

$can_edit = false;
if ( ! empty($content)) {
    if (priv('approve')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/update', 'id="frmVideo"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv('posting')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/save', 'id="frmVideo"');
        $can_edit = true;
    }
}

if ($can_edit) {
    echo form_hidden('closethis', 0);
}
?>

<div class="page-header">
    <?php
    if (isset($content->id)) {
        $h2_title = 'Ubah';
    } else {
        $h2_title = 'Tambah';
    }

    if ( ! priv('posting')) {
        $h2_title = 'Tampilkan';
    }

    echo '<h2>'.$h2_title.' '.var_lang($current_sct->title).'</h2>';
    ?>
</div>

<div class="row-fluid subnav">
    <div class="span12 cmdbar">
        <div class="btn-group span12">
            <?php
                if ($can_edit) {
                    echo form_input('title', $title, 'class="form_title span11" id="title" data-required="Judul" placeholder="Judul" maxlength="120"');
                    echo '<input type="button" onclick="title_case()" class="btn tips btn-mini" value="A/a" title="Format teks menjadi besar kecil">';
                } else {
                    echo $title;
                }
            ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
        <?php
            if ($can_edit) {
                echo '<h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>:</h5>';
                if ( priv('approve')) {
                    echo '  <label class="checkbox pull-left" style="margin:6px 24px 0 0;">
                                <input type="checkbox" name="active"'.( $active == 1 ? ' checked="checked"':'').'> Aktif
                            </label>';
                }
                echo '<div class="pull-left datetimepicker input-append">
                          '.form_input('pub_date', $pub_date, 'class="form_datepicker input-medium tips" title="Tanggal dipublikasikan" maxlength="19"').'
                          <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                      </div>
                      <div class="clearfix"></div>';

                echo '<h5 class="detail-title">Deskripsi <small><em>(excerpt/lead)</em></small>:</h5>';
                echo form_textarea(array('name'=>'description', 'value' => $description, 'id' => 'description', 'class'=>'span12', 'style'=>'height:150px'));

                echo '<h5 class="detail-title">Tags <small><em>(keywords)</em></small>:</h5>';
                echo form_input('tags', $tags, 'id="tags" class="span12 tagManager tips" placeholder="Tags/Keywords" title="Pisahkan dengan koma"');
            } else {
                echo '<p>Publikasi : '.($active==1?'Ya':'Tidak').'</p>';
                echo '<p>Tanggal : '.$pub_date.'</p>';
                echo '<p>Deskripsi:</p><p>'.$description.'</p>';
                echo '<p>Tags : '.$tags.'</p>';
            }
        ?>
    </div>
    <div class="span8">
        <?php
            if ($can_edit) {
                echo '<h2>Video Dari Youtube:</h2>';
                echo '<b>Youtube ID</b> ' . form_input('youtube_id', $youtube_id)
                    .'<p>Ambil URL youtube: http://youtube.com/watch?v=<strong>6lYc0QmAKn8</strong><br>Copy pastekan 6lYc0QmAKn8 ke kolom diatas</p>';
            }

            if ( ! empty($youtube_id)) {
                echo '<h5 class="detail-title">Preview Youtube:</h5>';
                echo '<iframe width="420" height="315" src="http://www.youtube.com/embed/' . $youtube_id . '" frameborder="0" allowfullscreen></iframe>';
                echo '<hr>';
            }

            if ( ! empty($image)) {
                echo '<h5 class="detail-title">Gambar Preview:</h5>';
                echo '<img src="' . base_url($image) . '" alt="Gambar" class="img-polaroid">';
                echo '<p><a href="' . site_url('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/del_image/' . $content->id) . '" style="color:#ff0000" class="askdelete" title="File Gambar"> Hapus </a></p>';
                echo '<hr>';
            }

            if ( ! empty($video_file)) {
                echo '<h5 class="detail-title">File Video:</h5>';
                echo '<a href="' . base_url($video_file) . '">' . $video_file . '</a>';
                echo '<p><a href="' . site_url('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/del_video/' . $content->id) . '" style="color:#ff0000" class="askdelete" title="File Video"> Hapus </a></p>';
                echo '<hr>';
            }

            if ($can_edit) {
                echo '<h3>Upload Video:</h3>';
                echo '<table class="table">'
                    .'  <tr>'
                    .'      <th>Gambar Preview</th>'
                    .'      <td>' .form_upload('image') . '</td>'
                    .'  </tr><tr>'
                    .'      <th>File Video (flv/mp4)</th>'
                    .'      <td>' .form_upload('video_file') . '</td>'
                    .'  </tr>'
                    .'</table>'
                    .'<hr>';
            }
        ?>

        <div class="well">
            <div class="pull-right">
                <div class="btn-group">
                    <?php if ($can_edit) { ?>
                        <button type="button" onclick="simpan()" class="btn"><span>Simpan</span></button>
                        <button type="button" onclick="simpan_tutup()" class="btn btn-primary"><span>Simpan &amp; Tutup</span></button>
                    <?php } ?>
                    <button type="button" onclick="tutup()" class="btn btn-inverse"><span>Tutup</span></button>
                </div>
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
    <?php if ($can_edit) { ?>

    $(document).ready(function() {
      var tmp = $("#tags").val().split(',');
      $("#tags").tagsManager().val('');
      for(var i=0;i<tmp.length;i++) {
        $(".tagManager").tagsManager('pushTag',tmp[i]);
      }
    });

    function simpan()
    {
        var keys = '';
        $('.myTag').each(function () {
        var a = $(this).find('span').text().trim();
            keys += ','+a;
        });
        $("#tags").val(keys.substr(1,keys.length));
        $('#frmVideo').submit();
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
        $('#frmVideo').submit();
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
       location.href= '<?php echo $return_url; ?>';
    }
</script>
