<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

include 'header.php';
?>
<div class="page-header">
    <h2 class="pull-left">Menu</h2>
    <div class="pull-right" style="margin: 20px 18px 0 10px">
        <a href="<?php echo site_url ('admin/menu_add'); ?>" class="btn btn-primary tips" title="Tambah Menu Baru">Tambah</a>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
        <table id="tdata" class="table table-striped table-bordered table-hover" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th width="16"></th>
                    <th width="16"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($menu as $m) {
                        echo '<tr>'
                            .'  <td>'.$m->title.'</td>'
                            .'  <td>'.$m->description.'</td>'
                            .'  <td><a href="'.site_url ('admin/menu_edit/'.$m->id).'" class="tips" title="Ubah '.$m->title.'"><i class="icon-edit"></i></a></td>'
                            .'  <td><a href="'.site_url ('admin/menu_del/'.$m->id).'" class="tips" title="Hapus '.$m->title.'"><i class="icon-trash"></i></a></td>'
                            .'</tr>';
                    }
                ?>
            </tbody>
        </table>
        <div class="clearfix"></div>
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
            { "sWidth": "16", "aTargets": [ 2,3 ] }
        ]
    });
});
</script>

<?php
include 'footer.php';
