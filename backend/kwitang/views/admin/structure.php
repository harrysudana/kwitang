<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';

function getChild($structure, $depth = 0)
{
    $ret = '';

    foreach ($structure as $s) {
        $desc = '';
        $xxx = get_structure_sct($s->id);
        if ( ! empty ($xxx)) {
            foreach ($xxx as $yyy) {
                $desc .= '<span class="tips label label-info" title="'.var_lang($yyy->title).' ('.$yyy->name.')">'
                   .$yyy->content_type.' <a href="'.site_url ('admin/structure_ct_edit/'.$yyy->id.'/'.$s->id).'"><i class="icon-edit icon-white"></i></a>'
                   .'</span> ';
            }
        }
        $s_title = var_lang($s->title);
        $ret .='<tr>'
              .'    <td>'.(($depth > 0) ? '<span>'.str_repeat('&nbsp;', $depth*4).(($depth>0) ?'&raquo;':'').'</span>' : '')
              .'          <a href="'.site_url ('admin/structure_edit/'.$s->id).'" class="tips" title="'.$s->name.'">'.$s_title.'</a>'
              .'    </td>'
              .'    <td>'.$desc.'</td>'
              .'    <td>'.$s->view_file .'</td>'
              .'    <td><a class="tips" href="'.site_url ('admin/structure_add/'.$s->id).'" title="Tambah struktur di dalam '.$s_title.'"><i class="icon-plus-sign"></i></a></td>'
              .'    <td><a class="tips" href="'.site_url ('admin/structure_edit/'.$s->id).'" title="Ubah '.$s_title.'"><i class="icon-edit"></i></a></td>'
              .'    <td><a class="tips" href="'.site_url ('admin/structure_delete/'.$s->id).'" title="Hapus '.$s_title.'" onclick="return (confirm(\'Anda yakin akan menghapus '.htmlentities($s_title).' ?\'))"><i data-placement="left" class="icon-trash icon-red"></i></a></td>'
              .'</tr>';

        if ( $depth < 30) {  // max 30 leaves
            if ( ! empty ($s->childs))
            $ret .= getChild($s->childs, $depth + 1);
        }
    }

    return $ret;
}
?>

<div class="page-header">
    <h2>Struktur</h2>
</div>

<div class="row-fluid">
    <div class="span12">
        <table id="tdata" class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th>Struktur</th>
                    <th>Konten</th>
                    <th>View File</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td colspan="6">
                        <a href="<?php echo site_url ('admin/structure_add'); ?>" class="btn btn-primary btn-mini tips" title="Tambah Struktur utama">
                            <i class="icon icon-white icon-plus-sign"></i>
                            Tambah Baru
                        </a>
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php
                echo getChild($structure_tree);
                ?>
            </tbody>
        </table>
        <div class="clearfix"></div>

        <br><br>
        <p>
            <i class="icon-info-sign"></i>
            Note:<br>
            <strong>SCT</strong>: Structure Content Type<br>
            <strong>CT</strong>: Content Type
        </p>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tdata').dataTable({
        "bSort": false,
        "bPaginate": false,
        "oLanguage": {
            "sLengthMenu": "Tampilkan _MENU_ data per halaman"
        },
        "aoColumnDefs": [
            { "sWidth": "90", "aTargets": [ 2 ] },
            { "sWidth": "16", "aTargets": [  3, 4, 5 ] }
        ],
        "bAutoWidth": false
    });
});
</script>

<?php
include 'footer.php';
