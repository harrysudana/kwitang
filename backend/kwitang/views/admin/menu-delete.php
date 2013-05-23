<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

include 'header.php';
?>
<div class="page-header">
    <h2>Konfirmasi Penghapusan Menu "<?php echo $curr_menu->title; ?>"</h2>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
        <p>Dengan menghapus menu ini, juga akan menghapus seluruh menu yang ada di bawahnya.</p>
        <p>Tekan tombol lanjutkan jika Anda yakin, dan tekan batal untuk kembali ke halaman sebelumnya.</p>
        <br>
        <div style="background-color: #CCCCCC;padding: 4px;">
            <div class="pull-left"><input type="button" id="bBatal" value=" Batal" class="btn" onclick="batal();"></div>
            <div class="pull-right"><input type="button" value=" Lanjutkan" class="btn btn-primary" onclick="lanjutkan()"></div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script type="text/javascript">
    function lanjutkan()
    {
        location.href= "<?php echo site_url ('admin/menu_del_confirm/'.$menu_id_to_be_delete); ?>";
    }

    function batal()
    {
        history.back();
    }

    $(function() {
        $("#bBatal").focus();
    })
</script>

<?php
include 'footer.php';
