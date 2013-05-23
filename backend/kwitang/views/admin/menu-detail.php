<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

include 'header.php';

function menu_detail($menu_detail, $parent = 0, $depth = 0)
{
    $ret = '';

    if ( ! empty ($menu_detail[$parent])) {
        foreach ($menu_detail[$parent] as $s) {
            $icon = ( ! empty ($s->icon) ? '<img src="'.base_url ($s->icon).'" alt="Ikon" height="24">' : '');
            $ret .= '<tr>'
                   .'    <td>'.$icon .'</td>'
                   .'    <td>'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth).(($depth>0) ?'&raquo;':'').' '.$s->title.'</td>'
                   .'    <td>'.$s->url .'</td>'
                   .'    <td><a href="'.site_url ('admin/menu_detail_add/'.$s->menu_id.'/' .$s->id).'" class="tips" alt="Add Child"><i class="icon-plus"></i></a></td>'
                   .'    <td><a href="'.site_url ('admin/menu_detail_edit/'.$s->id).'" class="tips" alt="Edit"><i class="icon-edit"></i></a></td>'
                   .'    <td><a href="'.site_url ('admin/menu_detail_delete/'.$s->id).'" onclick="return (confirm(\'Anda yakin akan menghapus '.htmlentities($s->title).' ?\'))" class="tips" alt="Delete"><i class="icon-trash"></i></a></td>'
                   .'</tr>';

            $new_parent = $s->id;
            if ( $depth <= 30) {  // max 30 leaves
                $new_depth = $depth + 1;
                $ret .= menu_detail($menu_detail, $new_parent, $new_depth);
            }
        }
    }

    return $ret;
}
?>
<div class="page-header">
    <h2><?php echo $menu->title; ?></h2>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span4">
        <?php
        echo form_open('admin/menu_save', 'class="form-horizontal"');
        echo form_hidden('id', $menu->id);
        ?>
        <div class="control-group">
            <label class="control-label" for="title">Judul</label>
            <div class="controls">
                <?php echo form_input('title', $menu->title, 'id="title" size="40" maxlength="45" rel="required|Judul" class="form_title"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="description">Keterangan</label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'description', 'value'=>$menu->description,'id'=>'description', 'rows'=>3, 'cols'=>40)); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="btnSubmit"></label>
            <div class="controls">
                <?php echo form_submit('btnSubmit', ' Simpan Perubahan ', 'class="btn"'); ?>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>

    <div class="span8">
        <div>
            <a href="<?php echo site_url ('admin/menu_detail_add/'.$menu->id); ?>" class="btn btn-primary">
                <i class="icon-plus icon-white"></i> Tambah Menu Detail
            </a>
            <div class="clearfix"></div>
            <hr>
        </div>
        <table id="tdata" class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th width="24"></th>
                    <th>Judul</th>
                    <th>URL</th>
                    <th width="16"></th>
                    <th width="16"></th>
                    <th width="16"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $menudel = array();
                if ( ! empty ($menu_detail)) {
                    foreach ($menu_detail as $mede) {
                        $menudel[$mede->parent_id][] = $mede;
                    }
                ?>
                        <?php
                        if ( ! empty ($menudel))
                            echo menu_detail($menudel);
                        ?>
                <?php
                } else {
                    echo '<tr><td colspan="6" class="txt-center">Menu masih kosong.</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="clearfix"></div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12">
        <div class="pull-left">
            <a href="<?php echo site_url ('admin/menu_del/'.$menu->id); ?>" class="btn btn-link" style="color: #FF0000"><i class="icon-trash icon-red"></i> Hapus Menu ini</a>
        </div>

    </div>
</div>

<?php
include 'footer.php';
