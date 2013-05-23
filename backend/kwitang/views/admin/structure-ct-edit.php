<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';

$structure_dropdown = structure_dropdown($structure_tree, $edit_sct->id);

function structure_dropdown($structure, $current_id, $deep = 1)
{
    $retval = ($deep == 1) ? array('Root') : '';
    foreach ($structure as $s) {
        $child = '';
        $retval[$s->id] = str_repeat('&nbsp;', $deep * 4).var_lang($s->title);
        if ( ! empty ($s->childs)) {
            $child = structure_dropdown($s->childs, $current_id, $deep + 1);
        }
        if (is_array ($child)) {
            foreach ($child as $k=>$v)
                $retval[$k] = $v;
        }
    }

    return $retval;
}
?>
<div class="container">
    <div class="page-header">
        <h1><?php echo var_lang($edit_sc->title).' &rarr; '.var_lang($edit_sct->title); ?></h1>
    </div>

    <div class="row">
        <div class="span12">
            <?php
            echo form_open_multipart(site_url ('admin/structure_ct_update'), 'id="frmStructure" class="form-horizontal"');
            echo form_hidden('id', $edit_sct->id);
            ?>
            <div class="control-group">
                <label class="control-label">Content Type</label>
                <div class="controls">
                    <span data-placement="left" class="tips badge badge-important" title="CT"><?php echo $edit_sct->content_type; ?></span>
                </div>
            </div>
            <hr>
            <div class="control-group">
                <label class="control-label" for="title">Judul</label>
                <div class="controls">
                    <?php
                        $langs = kconfig ('system', 'langs');
                            if ( ! $langs) {
                                echo form_input('title', var_lang($edit_sct->title), 'id="title" class="form_title"');
                            } else {
                                $langs = json_decode($langs);
                                $tmp1 = '';
                                $tmp2 = '';
                                $i = 0;
                                foreach ($langs as $val) {
                                    $tmp1 .= form_input('title['.$val->code.']', var_lang($edit_sct->title, $val->code), 'id="title_'.$val->code.'" class="'.($i==0?'active ':'').'" maxlength="60" data-required="Judul '.$val->code.'"');
                                    $tmp2 .= '<a href="#title_'.$val->code.'" class="tips '.($i==0?'active ':'').'">'.strtoupper($val->code).'</a>';
                                    $i++;
                                }
                                echo '<div class="lang-input">
                                        <div class="lang-content">
                                            '.$tmp1.'
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="lang-control">
                                            '.$tmp2.'
                                        </div>
                                    </div>';
                            }
                        ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="name">Nama Unik</label>
                <div class="controls">
                    <?php echo form_input('name', $edit_sct->name, 'id="name" data-placement="left" class="tips input-small" title="SCT" maxlength="30"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="structure_id">Structure</label>
                <div class="controls">
                    <?php echo form_dropdown('structure_id', $structure_dropdown, $edit_sct->structure_id, 'id="structure_id"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="view_index">View <strong>Index</strong></label>
                <div class="controls">
                    <?php echo form_dropdown('view_index', $view_files, $edit_sct->view_index, 'id="view_index"'); ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="view_content">View <strong>Content</strong></label>
                <div class="controls">
                    <?php echo form_dropdown('view_content', $view_files, $edit_sct->view_content, 'id="view_content"'); ?>
                </div>
            </div>
            <div class="well">
                <input type="button" value=" &larr; Batal " onclick="history.back()" class="btn">
                <?php echo form_submit('btnSubmit', ' Update ', 'class="btn btn-primary pull-right"'); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
include 'footer.php';
