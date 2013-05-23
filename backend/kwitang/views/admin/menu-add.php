<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

include 'header.php';
?>
<div class="page-header">
    <h2>Tambah Menu</h2>
    <div class="clearfix"></div>
</div>

<div class="row-fluid">
    <div class="span12">
        <?php
            echo form_open_multipart('admin/menu_save', 'class="form-horizontal');
        ?>
        <div class="control-group">
            <label class="control-label" for="title">Judul</label>
            <div class="controls">
                <?php echo form_input('title', '', 'id="title" size="40" maxlength="45" rel="required|Judul"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="description">Keterangan</label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'description', 'id'=>'description', 'rows'=>3, 'cols'=>40)); ?>
            </div>
        </div>
        <div class="well">
            <input type="button" value=" &lArr; Batal " class="btn" onclick="history.back();">
            <div class="pull-right"><?php echo form_submit('btnSubmit', ' Simpan ', 'class="btn btn-primary"'); ?></div>
            <div class="clearfix"></div>
        </div>
        <?php
            echo form_close();
        ?>
        <div class="clearfix"></div>
    </div>
</div>

<?php
include 'footer.php';
