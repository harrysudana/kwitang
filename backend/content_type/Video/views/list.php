<?php
$controller_uri = 'admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/';
?>
<div class="page-header">
    <div class="pull-left"><h2><?php echo var_lang($current_sct->title); ?></h2></div>
    <div class="pull-right">
        <a class="btn btn-primary" href="<?php echo site_url('admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/add');?>">
            <i class="icon icon-white icon-plus"></i> Tambah
        </a>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
        <table id="dtable" class="table table-condensed table-bordered table-hover">
           <thead>
              <tr>
                  <th>Tanggal</th>
                  <th>Judul</th>
                  <th>Penulis</th>
                  <th>Aktif</th>
                  <?php
                  if (priv ('manage')) {
                    echo '<th></th>';
                  }
                  ?>
              </tr>
           </thead>
           <tbody></tbody>
        </table>
    </div>
</div>

<script>
$(function() {
    $('#dtable').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?php echo site_url($controller_uri . 'data_json')?>",
        "bAutoWidth": false,
        "bPaginate": true,
        "iDisplayLength": <?php echo kconfig ('system', 'item_perpage', 10); ?>,
        "bLengthChange": true,
        "bFilter": true,
        "bSort": true,
        "aaSorting": [[0,'desc']],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "Tampilkan _MENU_ baris per halaman",
            "sZeroRecords": "Tidak menemukan data."
        },
        "aoColumnDefs": [
            { "sWidth": "75", "sClass": "txt-center", "aTargets": [ 0 ] },
            { "sWidth": "60", "sClass": "txt-center", "aTargets": [ 2, 3 ] }
            <?php
            if (priv ('manage')) {
                echo ',{ "sWidth": "16", "bSortable" : false, "aTargets": [ 4 ] }';
            }
            ?>
        ]
    });
});
</script>
