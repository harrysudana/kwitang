<?php if ( ! defined ('FRONT_PATH')) exit ('CyberCMS Error...!');

include 'header.php';

$id      = 0;
$menu_id = 0;
$title   = '';
$icon    = '';
$url     = '';
$order   = 0;
if ( ! empty ($curr_menu_detail)) {
    $id        = $curr_menu_detail->id;
    $menu_id   = $curr_menu_detail->menu_id;
    $title     = $curr_menu_detail->title;
    $icon      = $curr_menu_detail->icon;
    $url       = $curr_menu_detail->url;
    $parent_id = $curr_menu_detail->parent_id;
    $order     = $curr_menu_detail->order;

    $the_title = '<h2>Edit Menu Item</h2>';
    echo form_open_multipart(site_url ('admin/menu_detail_save'));
    echo form_hidden('id', $id);
    echo form_hidden('menu_id', $curr_menu->id);
} else {
    $the_title = '<h2>Tambah Menu Item</h2>';
    echo form_open_multipart(site_url ('admin/menu_detail_save'));
    $order = $neworderval;
    echo form_hidden('menu_id', $curr_menu->id);
    echo form_hidden('parent_id', $parent_id);
}
?>
<div class="page-header">
    <?php echo $the_title; ?>
</div>
<div class="row-fluid form-horizontal">
    <div class="span12">
        <div class="control-group">
            <label class="control-label" for="name">Judul</label>
            <div class="controls">
                <?php echo form_input('title', $title, 'id="title" size="40" rel="required|Judul"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="url">URL</label>
            <div class="controls">
                <?php echo form_input('url', $url, 'size="60" rel="required|URL"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="order">Urutan</label>
            <div class="controls">
                <?php echo form_input('order', $order, 'size="2" class="span2"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="icon">Ikon</label>
            <div class="controls">
                <?php echo form_upload('icon', '', 'class="btn"'); ?>
            </div>
        </div>
        <div class="well">
            <a href="<?php echo empty ($curr_menu) ? 'history.back();' :site_url ('admin/menu_edit/'.$curr_menu->id); ?>" class="btn">&lArr; Batal</a>
            <div class="pull-right"><?php echo form_submit('btnSubmit', ' Simpan ', 'class="btn btn-primary"'); ?></div>
        </div>
    </div>
</div>
<?php
echo form_close();
if ( ! empty ($icon)) {
    echo '<br><hr><br>Ikon:<br>';
    echo '<img src="'.base_url ($icon). '" alt="Icon">';
}

include 'footer.php';
