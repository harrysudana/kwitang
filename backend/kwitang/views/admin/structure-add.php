<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Tambah Struktur</h1>
        <?php echo ( ! empty ($parent_data)) ? '<i class="icon-info-sign"></i> dibawah struktur <em>"' .var_lang($parent_data->title).'"</em>' : ''; ?>
    </div>

    <?php
        echo form_open_multipart(site_url ('admin/structure_save'), 'class="form-horizontal"');
        if( ! empty ($parent_data))
            echo form_hidden('parent_id', $parent_data->id);
    ?>
    <div class="row">
        <div class="span6">
            <div class="control-group">
                <label class="control-label" for="title">Judul</label>
                <div class="controls">
                    <?php
                        $langs = kconfig ('system', 'langs');
                        if ( ! $langs) {
                            echo form_input('title', '', 'id="title" class="form_title"');
                        } else {
                            $langs = json_decode($langs);
                            $tmp1 = '';
                            $tmp2 = '';
                            $i = 0;
                            foreach ($langs as $val) {
                                $tmp1 .= form_input('title['.$val->code.']', '', 'id="title_'.$val->code.'" class="'.($i==0?'active ':'').' lang_title" maxlength="60" data-required="Judul '.$val->code.'"');
                                $tmp2 .= '<a href="#title_'.$val->code.'" class="tips '.($i==0?'active ':'').'">'.strtoupper($val->code).'</a>';
                                $i++;
                            }
                            echo '<div class="lang-input">
                                    <div class="lang-content">
                                        '.$tmp1.'
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="lang-control">
                                        '.$tmp2.'
                                    </div>
                                </div>';
                        }
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="name"><small>Nama Unik</small></label>
                <div class="controls">
                    <?php echo form_input('name', '', 'id="name" size="20" maxlength="30" data-required="Nama Unik" class="tips" title="Tidak boleh mengandung spasi dan tanda baca, kolom ini dibuat secara otomatis."'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="in_menu">Tampil di Menu</label>
                <div class="controls">
                    <input type="checkbox" name="in_menu" id="in_menu" checked="checked">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="order">Urutan</label>
                <div class="controls">
                    <?php echo form_input('order', $neworderval, 'id="order" class="tips input-mini" title="Urutan penampilan dalam baris data"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="icon">Ikon</label>
                <div class="controls">
                    <?php echo form_upload('icon', '', 'id="icon" size="20"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="image">Gambar</label>
                <div class="controls">
                    <?php echo form_upload('image', '', 'id="image" size="20"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Penjelasan</label>
                <div class="controls">
                    <?php
                        $langs = kconfig ('system', 'langs');
                            if ( ! $langs) {
                                echo form_textarea(array('name'=>'description','id'=>'desc_id', 'class'=> 'active', 'cols'=> 33, 'rows' => 3));
                            } else {
                                $langs = json_decode($langs);
                                $tmp1 = '';
                                $tmp2 = '';
                                $i = 0;
                                foreach ($langs as $val) {
                                    $tmp1 .= form_textarea(array('name'=>'description['.$val->code.']','id'=>'desc_'.$val->code, 'class'=> ($i==0?'active':''), 'cols'=> 33, 'rows' => 3));
                                    $tmp2 .= '<a href="#desc_'.$val->code.'" class="tips '.($i==0?'active ':'').'">'.strtoupper($val->code).'</a>';
                                    $i++;
                                }
                                echo '<div class="lang-input">
                                        <div class="lang-content">
                                            '.$tmp1.'
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="lang-control">
                                            '.$tmp2.'
                                        </div>
                                    </div>';
                            }
                        ?>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="control-group">
                <label class="control-label" for="content_type">Tipe Konten</label>
                <div class="controls">
                    <?php
                    $ct = array(''=>'Pilih untuk menambahkan...');
                    $ct = array_merge($ct, $content_types);
                    echo form_dropdown('content_type', $ct, 'channel', 'id="content_type" class="tips" title="Tambahkan Tipe Konten, Setelah disimpan, Anda dapat menambahkan tipe-koten lainnya."');
                    ?>
                </div>
            </div>
            <div class="well">
                <p><strong>Tampilkan struktur dengan:</strong></p>
                <div class="control-group">
                    <label class="control-label" for="view_file">View File</label>
                    <div class="controls">
                        <?php
                        $view_files_show = array(''=>'N.A (Tampilkan Konten)');
                        if ($view_files) {
                            $view_files_show['Pilih View'] = $view_files;
                        }
                        echo form_dropdown('view_file', $view_files_show, '', 'id="view_file" class="tips" title="Pilih file view pada frontend untuk menampilkan data dari struktur ini"');
                        ?>
                    </div>
                </div>
                <div id="view_type">
                    <div class="control-group">
                        <label class="control-label">Konten</label>
                        <div class="controls">
                            <?php
                            echo form_radio('view_type', 'single', true).' Konten Terakhir<br>';
                            echo form_radio('view_type', 'index', false).' Indeks Konten ';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="span12">
            <div class="well">
                <?php echo form_button('btnCancel' , ' &larr; Batal ', 'onclick="history.back()" class="btn"'); ?>
                <?php echo form_submit('btnSubmit', ' Simpan ', 'class="btn btn-primary pull-right"'); ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <?php
    echo form_close();
    ?>
</div>

<script type="text/javascript">
$(function() {
    $("#title_<?php echo kconfig ('system', 'lang_default', 'id'); ?>").keyup(function() {
        var val = $(this).val();
        $(".lang_title").each(function() {
            if($(this).val() == "" || $(this).val() == val.substr(0, val.length-1)) {
                $(this).val(val);
            }
        });
        $("#name").val(slugify(val));
    });
    $("#view_file").change(function() {
        if ($(this).val() == "") {
            $("#view_type").show();
        } else {
            $("#view_type").hide();
        }
    });
    $(".form_title").keyup(function() {
        $("#name").val(slugify($(this).val()).substr(0, 20));
    });
});
</script>

<?php
include 'footer.php';
