<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <div class="pull-left"><h1>Pengguna</h1></div>
    <div class="pull-right"><br><a href="<?php echo site_url ('admin/user_add'); ?>" class="btn btn-primary pull-right">Tambah User</a></div>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
    <table id="tdata" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="32" align="center">Active</th>
                <th>Nama Lengkap</th>
                <th>username</th>
                <th>Login Terakhir</th>
                <th>Level</th>
                <th width="42">Log</th>
                <th width="16"></th>
                <th width="16"></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $num = 1;
                foreach ($users as $u) {
                    echo '<tr>'
                        .'  <td>'.(($u->active) ? '<i class="icon-ok icon-green"></i>' : '<i class="icon-remove icon-red"></i>').'</td>'
                        .'  <td>'.$u->fullname.'</td>'
                        .'  <td>'.$u->username.'</td>'
                        .'  <td>'.$u->email.'</td>'
                        .'  <td>'.strtoupper($u->level).'</td>'
                        .'  <td><a href="'.site_url ('admin/user_log/'.$u->username).'"><i class="icon-tasks"></i> Log &rarr;</a></td>'
                        .'  <td><a href="'.site_url ('admin/user_edit/'.$u->username).'"><i class="icon-edit"></i></a></td>'
                        .'  <td><a href="'.site_url ('admin/user_delete/'.$u->username).'" class="askdelete" title="'.$u->fullname.'" onclick="return false"><i class="icon-trash"></i></a></td>'
                        .'</tr>';

                    $num++;
                }
            ?>
        </tbody>
    </table>
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
            { "sWidth": "150", "aTargets": [ 3 ] },
            { "sWidth": "80", "aTargets": [ 0, 2, 4, 5 ] },
            { "sWidth": "16", "aTargets": [ 6, 7 ] }
        ],
        "bAutoWidth": false
    });
});
</script>

<?php
include 'footer.php';
