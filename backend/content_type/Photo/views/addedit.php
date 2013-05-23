<?php

$id          = ! empty($content->id) ? $content->id : '';
$title       = ! empty($content->title) ? $content->title : '';
$pub_date    = ! empty($content->pub_date) ? date('Y-m-d H:i:s', from_gmt($content->pub_date)) : date('Y-m-d H:i:s', from_gmt());
$description = ! empty($content->description) ? $content->description : '';
$active      = isset($content->active) ? $content->active : 1;
$tags        = ! empty($content->tags) ? $content->tags : '';

$can_edit = false;
if ( ! empty($content)) {
    if (priv('approve')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/update', 'id="frmPhoto"');
        echo form_hidden('id', $content->id);
        $can_edit = true;
    }
} else {
    if (priv('posting')) {
        echo form_open_multipart('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/save', 'id="frmPhoto"');
        $can_edit = true;
    }
}

if ($can_edit) {
    echo form_hidden('closethis', 0);
}

?>

<div class="page-header">
    <?php
        $h2_title = $can_edit ? 'Ubah' : 'Tampilkan';
        $h2_title = empty($content) ? 'Tambah' : $h2_title ;

        echo '<h2>'.$h2_title.' '.var_lang($current_sct->title).'</h2>';
    ?>
</div>

<div class="row-fluid">
    <div class="span12 cmdbar">
        <div class="btn-group span12">
            <?php
                if ($can_edit) {
                    echo form_input('title', $title, 'class="form_title span11" id="title" data-required="Judul" placeholder="Judul" maxlength="120"');
                    echo '<input type="button" onclick="title_case()" class="btn tips btn-mini" value="A/a" title="Format teks menjadi besar kecil">';
                } else {
                    echo '<p>'.$title.'</p>';
                }
            ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
        <h5 class="detail-title">Publikasi <small><em>(active/live)</em></small>:</h5>
        <?php
        if ( priv('approve')) {
            echo '  <label class="checkbox pull-left" style="margin:6px 24px 0 0;">
                        <input type="checkbox" name="active" '.($active==1 ? ' checked="checked"' : '').'> Aktif
                    </label>';
        } else {
            echo $active ? 'Aktif' : 'Tidak Aktif';
        }

        if ($can_edit) {
            echo '  <div class="datetimepicker input-append tips" title="Tanggal Publikasi">
                        '.form_input('pub_date', $pub_date, 'class="form_datepicker input-medium" maxlength="19"').'
                        <span class="add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
                    </div>';
        } else {
            echo '<p>'.$pub_date.'</p>';
        }

        echo '<h5 class="detail-title">Deskripsi <small><em>(excerpt/lead)</em></small>:</h5>';
        if ($can_edit) {
            echo form_textarea(array('name'=>'description',
                                     'value' => $description,
                                     'id' => 'description',
                                     'class'=>'span12',
                                     'style' => 'height:110px'));
        } else {
            echo '<p>'.$description.'</p>';
        }

        echo '<h5 class="detail-title">Tags <small><em>(keywords)</em></small>:</h5>';
        if ($can_edit) {
            echo form_input('tags', $tags, 'id="tags" class="span12 tagManager tips" placeholder="Tags/keywords" title="Pisahkan dengan koma"');
        } else {
            echo '<p>'.$tags.'</p>';
        }
        ?>
    </div>
    <div class="span8">
        <?php
        for ($i = 1; $i <= 20; $i++) {
            $f = 'foto' . $i;
            $d = 'description' . $i;
            if ($can_edit) {
                echo '  <div style="border-bottom: 1px solid #AAAAAA; margin-bottom: 12px;padding: 4px 6px">
                            <p>Foto <b>' . $i . '</b>: '.form_upload($f).'</p>
                            <p>Deskripsi : ' . form_input($d, isset($content->$d)?$content->$d:'', 'class="input-xxlarge" maxlength="255"').'</p>';
                echo ( ! empty($content->$f)) ? '<div class="pull-left"><a href="' . base_url($content->$f) . '" target="_blank"><i>' . basename($content->$f) . '</i></a></div>' : '';

                if ( priv('manage') AND ! empty($content->$f)) {
                  echo '    <div class="pull-right"><a href="' . site_url('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/del_foto/' . $content->id . '-' . $i) . '" style="color: #ff0000" class="askdelete" title="' . $f . '">Delete</a></div>';
                }

                echo '      <div class="clearfix"></div>
                        </div>';
            } else {
                echo '  <div style="border-bottom: 1px solid #AAAAAA; margin-bottom: 12px;padding: 4px 6px">
                            <p>Foto '.$i.': '.((! empty($content->$f)) ? '<a href="' . base_url($content->$f) . '" target="_blank"><i>' . base_url($content->$f) . '</i></a>' : '').'</p>
                            <p>Deskripsi: '.(isset($content->$d)?$content->$d:'').'</p>
                        </div>';
            }
        }
        ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
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
    if( $can_edit) {
        echo form_close();
    }
?>

<script type="text/javascript">
    <?php if( $can_edit) { ?>
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
        $('#frmPhoto').submit();
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
        $('#frmPhoto').submit();
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
