<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';

$structure_dropdown = structure_dropdown($structure_tree, $data->id);

function structure_dropdown($structure, $current_id, $deep = 1)
{
    $retval = ($deep == 1) ? array('Root') : '';
    foreach ($structure as $s) {
        if ($s->id == $current_id) {
            continue;
        }
        $child = '';
        $retval[$s->id] = str_repeat('&nbsp;', $deep * 4).var_lang($s->title);
        if ( ! empty ($s->childs)) {
            $child = structure_dropdown($s->childs, $current_id, $deep + 1);
        }
        if (is_array ($child)) {
            foreach ($child as $k=>$v)
                $retval[$k] = $v;
        }
    }

    return $retval;
}
?>
<div class="container">
    <div class="page-header">
        <h1>Struktur &rarr; <?php echo var_lang($data->title); ?></h1>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#structure" data-toggle="tab">Struktur</a></li>
        <li><a href="#content-type" data-toggle="tab">Konten (SCT)</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="structure">
            <?php
                echo form_open_multipart(site_url ('admin/structure_update'), 'id="frmStructure" class="form-horizontal"');
                echo form_hidden('id', $data->id);
            ?>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <label class="control-label" for="parent_id">Parent</label>
                        <div class="controls">
                            <?php echo form_dropdown('parent_id', $structure_dropdown, $data->parent_id, 'id="parent_id"'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="title">Judul</label>
                        <div class="controls">
                            <?php
                                $langs = kconfig ('system', 'langs');
                                if ( ! $langs) {
                                    echo form_input('title', var_lang($data->title), 'id="title" class="form_title"');
                                } else {
                                    $langs = json_decode($langs);
                                    $tmp1 = '';
                                    $tmp2 = '';
                                    $i = 0;
                                    foreach ($langs as $val) {
                                        $tmp1 .= form_input('title['.$val->code.']', var_lang($data->title, $val->code), 'id="title_'.$val->code.'" class="'.($i==0?'active ':'').'" maxlength="60" data-required="Judul '.$val->code.'"');
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
                            <?php echo form_input('name', $data->name, 'id="name" class="form_slug" maxlength="30"'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <label class="checkbox">
                                <input type="checkbox" name="in_menu" id="in_menu" <?php echo $data->in_menu == 1 ? 'checked="checked"' : ''; ?> >
                                Tampil di menu
                            </label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="order">Urutan</label>
                        <div class="controls">
                            <?php echo form_input('order', $data->order, 'id="order" size="2" class="input-mini"'); ?>
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
                            <div class="lang-input">
                                <?php
                                    $langs = kconfig ('system', 'langs');
                                    if ( ! $langs) {
                                        echo form_textarea(array('name'=>'description','id'=>'desc_id', 'class'=> 'active', 'value'=>var_lang($data->description), 'cols'=> 33, 'row-fluids' => 3));
                                    } else {
                                        $langs = json_decode($langs);
                                        $tmp1 = '';
                                        $tmp2 = '';
                                        $i = 0;
                                        foreach ($langs as $val) {
                                            $tmp1 .= form_textarea(array('name'=>'description['.$val->code.']','id'=>'desc_'.$val->code, 'class'=> ($i==0?'active':''), 'value'=>var_lang($data->description, $val->code), 'cols'=> 33, 'row-fluids' => 3));
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
                </div>
                <div class="span6">
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
                                echo form_dropdown('view_file', $view_files_show, $data->view_file, 'id="view_file"'); ?>
                            </div>
                        </div>
                        <div id="view_type">
                            <div class="control-group">
                                <label class="control-label" for="view_sct">Konten (SCT)</label>
                                <div class="controls">
                                    <?php echo form_dropdown('view_sct', $view_childs, $data->view_sct, 'id="view_sct"'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Konten</label>
                                <div class="controls">
                                    <?php
                                    $is_single = ($data->view_type == 'single' ? true : false);
                                    echo '<label class="checkbox">'.form_radio('view_type', 'single', $is_single).' Konten Terakhir</label>';
                                    echo '<label class="checkbox">'.form_radio('view_type', 'index', ! $is_single).' Indeks Konten </label>';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6" style="max-height:200px;overflow:hidden">
                    <?php
                    if ( ! empty ($data->icon)) {
                        echo '  <p><strong>Ikon: </strong></p>'
                            .'  <img style="border:1px solid #FFFFFF" src="'.base_url ($data->icon).'" alt="'.$data->icon.'">'
                            .'  <hr><a class="askdelete btn btn-danger" href="'.site_url ('admin/delicon/'.$data->id).'" title="'.$data->icon.'">Hapus gambar</a>';
                    }
                    ?>
                    <div class="clearfix"></div>
                </div>
                <div class="span6" style="max-height:200px;overflow:hidden">
                    <?php
                    if ( ! empty ($data->image)) {
                        echo '  <p><strong>Gambar: </strong></p>'
                            .'  <img style="border:1px solid #FFFFFF" src="'.base_url ($data->image).'" alt="'.$data->image.'">'
                            .'  <hr><a class="askdelete btn btn-danger" href="'.site_url ('admin/delimage/'.$data->id).'" title="'.$data->image.'">Hapus gambar</a>';
                    }
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>
            <br>
            <div class="row-fluid">
                <div class="span12">
                    <div class="well">
                        <?php echo form_submit('btnSubmit', ' Simpan Struktur ', 'class="btn btn-primary pull-right"'); ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div class="tab-pane" id="content-type">
            <div class="row-fluid">
                <div class="span12">
                    <h4>Structure Content-Type (SCT)</h4>
                    <p>Tipe konten adalah jenis dari data yang disimpan, dapat berupa artikel, halaman, agenda dll</p>

                    <a href="#myModal" role="button" class="btn btn-primary" data-toggle="modal"><i class="icon-plus-sign icon-white"></i> Tambah SCT</a>
                    <?php if( ! empty ($data_sct_edit)) { ?>
                    <table class="table table-condensed table-bordered table-hover">
                        <thead>
                            <tr>
                                <th rowspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;">Judul</th>
                                <th rowspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;">Nama Unik</th>
                                <th rowspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;">Tipe Konten</th>
                                <th colspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;">View File</th>
                                <th rowspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;" width="20"></th>
                                <th rowspan="2" style=" padding: 1px 8px;margin: 0;text-align:center;" width="20"></th>
                            </tr><tr>
                                <th style=" padding: 1px 8px;margin: 0;text-align:center;">Index</th>
                                <th style=" padding: 1px 8px;margin: 0;text-align:center;">Content</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data_sct_edit as $d) {
                                echo '<tr>'
                                    .'  <td>'.var_lang($d->title).'</td>'
                                    .'  <td>'.$d->name.'</td>'
                                    .'  <td><span class="badge badge-important">'.$d->content_type.'</span></td>'
                                    .'  <td>'.$d->view_index.'</td>'
                                    .'  <td>'.$d->view_content.'</td>'
                                    .'  <td><a href="'.site_url ('admin/structure_ct_edit/'. $d->id .'/'. $data->id) .'" class="editsct" rel="'. $d->id .'" title="'.var_lang($d->title).'">'
                                    .'      <i class="tips icon-edit" title="Ubah '.var_lang($d->title).'"></i>'
                                    .'  </a></td>'
                                    .'  <td><a href="'.site_url ('admin/structure_ct_delete/'. $d->id .'/'. $data->id .'/86') .'" class="askdelete" title="'.var_lang($d->title).'">'
                                    .'      <i class="tips icon-trash" title="Hapus '.var_lang($d->title).'"></i>'
                                    .'  </a></td>'
                                    .'</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo form_open('admin/structure_ct_save', 'class="form-horizontal"');
echo form_hidden('id', $data->id);
?>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Tambah Konten (SCT) Baru</h3>
    </div>
    <div class="modal-body">
        <div class="control-group">
            <label class="control-label" for="ct_title">Judul</label>
            <div class="controls">
                <?php echo form_input('ct_title', '', 'id="ct_title"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ct_name"><small>Nama Unik</small></label>
            <div class="controls">
                <?php echo  form_input('ct_name', '', 'id="ct_name" data-placement="left" class="tips" title="SCT Name"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="content_type">Tipe Konten</label>
            <div class="controls">
                <?php echo form_dropdown('content_type', $content_types, '' ,'id="content_type" data-placement="left" class="tips" title="CT (Content Type)"'); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <?php echo form_submit('ctAddSubmit', ' Tambah', 'class="btn btn-primary"'); ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(function() {
        view_option();
        $("#view_file").change(function() {view_option();});
    });
    function view_option() {
        if ($("#view_file").val() == '') {
            $("#view_type").show();
        } else {
            $("#view_type").hide();
        }
    }
</script>

<?php
include 'footer.php';
