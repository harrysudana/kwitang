<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <h2><?php echo lang('k_content_type'); ?></h2>
    <div class="clearfix"></div>
</div>

<?php
if( ! empty ($content_types)) {
    foreach ($content_types as $key => $ct) {
?>
<div class="row-fluid">
    <div class="span12">
        <table class="table table-bordered table-hover dataTable">
            <thead>
                <th style="width:120px">Nama</th>
                <th>Judul</th>
                <th style="width:120px">Versi</th>
                <th style="width:120px;text-align:center;"></th>
                <th style="width:120px;text-align:center;"></th>
            </thead>
            <tbody>
                <?php
                    $num = 1;
                    foreach ($ct as $c) {
                        $setting = '';
                        if ($c['setting']) {
                            $setting = '<a href="#">Setting</a>';
                        }

                        $install = '';
                        if ($c['installed'] === true) {
                            $install = '<small><a href="javascript:ct_uninstall(\''.$c['name'].'\',\''.site_url('admin/uninstall_ct/'.$c['name']).'\');" class="tips" title="Drop tabel pada tipe-konten ini" style="color:#f00">'
                                      .'<i class="icon-trash icon-red"></i> Uninstall'
                                      .'</a></small>';
                        } elseif ($c['installed'] === false) {
                            $install = '<a href="'.site_url('admin/install_ct/'.$c['name']).'" class="btn tips" title="Create tabel di database">'
                                      .'<i class="icon-download-alt"></i> Install'
                                      .'</a>';
                        }
                        echo '<tr>'
                            .'<td>'.$c['name']
                            .($key != 'backend' ? '<br><small class="tips" title="Install location of this Content-Type"><i>'.ucfirst($key).'</i></small>':'')
                            .'</td>'
                            .'<td>'.$c['title'].'</td>'
                            .'<td>'.$c['version'].'</td>'
                            .'<td style="width:120px;text-align:center;">'.$setting.'</td>'
                            .'<td style="width:120px;text-align:center;">'.$install.'</td>'
                            .'</tr>';
                        $num++;
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="ctUninstall" class="modal hide fade">
    <div class="modal-header" style="background: #f00;color:#fff">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Hapus Tipe-Konten "<span class="ct-title-name"></span>"</h3>
    </div>
    <div class="modal-body">
        <p>Jika Anda melanjutkan menghapus Tipe-Konten <strong><span class="ct-title-name"></span></strong></p>
        <p>Anda akan menghapus tabel tipe konten tersebut dari database. Hal ini akan mengakibatkan seluruh data yang ada pada tipe konten tersebut <strong>akan Hilang untuk selamanya</strong>.</p>
        <p>Struktur yang menggunakan tipe-konten ini akan berhenti bekerja setelah penghapusan, Anda dapat menghapus kaitan tersebut (SCT) pada edit struktur.</p>
        <p>Pastikan Anda telah melakukan backup pada database sebelum melanjutkan.</p>
    </div>
    <div class="modal-footer">
        <a href="#" id="ct-link-delete" class="btn btn-danger"><i class="icon-warning-sign icon-white"></i> Lanjutkan Menghapus Tipe Konten</a>
        <a href="#" id="ct-dialog-close" class="btn btn-primary">Tutup</a>
    </div>
</div>

<div id="helpScreen" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Tipe Konten</h3>
    </div>
    <div class="modal-body">
        <p>Tipe-konten adalah modul yang akan menyimpan suatu jenis konten tertentu. Pada halaman ini Anda dapat memasang dan menghapus tipe-konten.</p>
        <p>Pemasangan tipe konten umumnya dilkukan pada awal membangun website. Dan hampir tidak pernah diperlukan untuk menghapus sebuah tipe-konten yang telah dipasang & digunakan.</p>
        <p>Sebelum tipe-konten dapat digunakan pada Struktur, tipe-konten tersebut harus dipasang <i>(install)</i> terlebih dahulu.</p>
    </div>
    <div class="modal-footer">
        <a href="javascript:$('#helpScreen').modal('hide');" class="btn">Close</a>
    </div>
</div>

<script>
$(function() {
    $("#ct-dialog-close").click(function() {
        $(".ct-title-name").text("");
        $("#ct-link-delete").attr("href", "#");
        $("#ctUninstall").modal("hide");
    });
});

function ct_uninstall(title, url) {
    $(".ct-title-name").text(title);
    $("#ct-link-delete").attr("href", url);
    $("#ctUninstall").modal("show");
}
</script>
<?php
    }
} else {
    echo '<div class="row-fluid">'
        .'  <div class="span12">'
        .'    <p>Tidak ada Tipe konten.</p>'
        .'  </div>'
        .'</div>';
}

include 'footer.php';
