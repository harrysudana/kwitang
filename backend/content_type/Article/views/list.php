<?php
$controller_uri = 'admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/';

$langs = kconfig ('system', 'langs');
$langs = json_decode($langs);
?>
<div class="page-header">
    <div class="pull-left">
        <h2><?php echo var_lang($current_sct->title); ?></h2>
    </div>
    <?php
        echo '<div class="pull-right">';
        if (priv ('posting')) {
            echo '<a class="btn btn-primary" href="'.site_url('admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/add').'"><i class="icon icon-white icon-plus"></i>Tambah</a>';
        }
        if (priv('manage')) {
            echo '  <div class="btn-group">
                        <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon icon-wrench icon-white"></i>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="'.site_url('admin/setting_ct/Article/get_rss/'.$current_sct->id).'">Get Content</a></li>
                            <li><a href="'.site_url('admin/setting_ct/Article/setting/'.$current_sct->structure_id.'/'.$current_sct->id).'">Setting</a></li>
                        </ul>
                    </div>';
        }
        echo '</div>';
    ?>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
        <table id="dtable" class="table table-condensed table-bordered table-hover">
           <thead>
              <tr>
                  <th>Tanggal</th>
                  <?php
                  if (is_array($langs) && count($langs) > 1) {
                    echo '<th>Bahasa</th>';
                  }
                  ?>
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
        "sAjaxSource": "<?php echo site_url($controller_uri.'data_json')?>",
        "bAutoWidth": false,
        "bPaginate": true,
        "iDisplayLength": <?php echo kconfig ('system', 'item_perpage', 10); ?>,
        "bLengthChange": true,
        "bFilter": true,
        "bSort": true,
        "aaSorting": [[0,"desc"]],
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "Tampilkan _MENU_ baris per halaman",
            "sZeroRecords": "Tidak menemukan data."
        },
        "aoColumnDefs": [
            { "sWidth": "75", "sClass": "txt-center", "aTargets": [ 0 ] }
            <?php
                $idx = 0;
                if (is_array($langs) && count($langs) > 1) {
                    $idx++;
                    echo ',{ "sWidth": "60", "sClass": "txt-center", "bSortable": false, "aTargets": [ 1 ] }';
                }
                echo ',{ "sWidth": "60", "sClass": "txt-center", "aTargets": [ '.(2+$idx).', '.(3+$idx).' ] }';

                if (priv ('manage')) {
                    echo ',{ "sWidth": "16", "bSortable" : false, "aTargets": [ '.(4+$idx).' ] }';
                }
            ?>
        ]
    });
});
</script>
