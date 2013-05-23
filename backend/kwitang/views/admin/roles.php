<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <h2 class="pull-left">Hak Akses</h2>
    <div class="pull-right" style="margin: 20px 18px 0 10px">
        <a href="<?php echo site_url ('admin/roles_add'); ?>" class="btn btn-primary tips" title="Tambah Hak Akses">Tambah</a>
    </div>
    <div class="clearfix"></div>
</div>
<div class="row-fluid">
    <div class="span12">
        <table class="table table-striped table-hover table-bordered dataTable">
            <thead>
                <th width="40">ID</th>
                <th>Role</th>
                <th>Catatan</th>
                <th width="20"></th>
                <th width="20"></th>
            </thead>
            <tbody>
                <?php
                if( ! empty ($roles)) {
                    foreach ($roles as $r) {
                        echo '<tr>'
                            .'<td>'.$r->id.'</td>'
                            .'<td>'.$r->title.'</td>'
                            .'<td>'.$r->notes.'</td>'
                            .'<td><a href="'.site_url ('admin/roles_edit/'.$r->id).'"><i class="icon-edit"></i></a></td>'
                            .'<td><a href="'.site_url ('admin/roles_delete/'.$r->id).'" class="askdelete" title="'.$r->title.'"><i class="icon-trash"></i></a></td>'
                            .'</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'footer.php';
