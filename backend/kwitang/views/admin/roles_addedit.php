<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';

$role_name  = ! empty ($roles_edit->title) ? $roles_edit->title : '';
$role_notes = ! empty ($roles_edit->notes) ? $roles_edit->notes : '';

$role = array();
if ( ! empty ($role_detail_edit)) {
    foreach ($role_detail_edit as $r) {
        $role[$r->structure_id] = strtolower($r->permission);
    }
}

$ROLE_NAME = array('noaccess' => 'No Access',
                   'view'     => 'View',
                   'posting'  => 'Posting',
                   'approve'  => 'Approve',
                   'manage'   => 'Manage');

function getChild($structure, $role, $depth = 0)
{
    $retval = '<ul class="ul-roles">';

    foreach ($structure as $s) {
        $style = ($depth ==0) ? 'padding:4px 6px;background:#f5f5f5;border: 1px solid #ddd;margin-bottom: 4px' : 'padding: 2px 6px;border-bottom: 1px dotted #aaa; margin-bottom: 4px';

        $retval .='<li>'
                 .'<div class="prow" style="'.$style.'">'
                 .'    <div class="tips" title="'.var_lang($s->description).'" style="display:inline;">'.var_lang($s->title).'</div>'
                 .'    <div style="display: inline;float: right">';

        $retval .='        <input class="role-detail" type="radio" name="role['.$s->id.']" value="noaccess" '.((empty ($role[$s->id]) OR $role[$s->id] == 'noaccess') ? 'checked="checked"' : '') .'> No Access'
                 .'        <input class="role-detail" type="radio" name="role['.$s->id.']" value="view" '.((isset ($role[$s->id]) AND $role[$s->id] == 'view') ? 'checked="checked"' : '') .'> View'
                 .'        <input class="role-detail" type="radio" name="role['.$s->id.']" value="posting" '.((isset ($role[$s->id]) AND $role[$s->id] == 'posting') ? 'checked="checked"' : '') .'> Posting'
                 .'        <input class="role-detail" type="radio" name="role['.$s->id.']" value="approve" '.((isset ($role[$s->id]) AND $role[$s->id] == 'approve') ? 'checked="checked"' : '') .'> Approve'
                 .'        <input class="role-detail" type="radio" name="role['.$s->id.']" value="manage" '.((isset ($role[$s->id]) AND $role[$s->id] == 'manage') ? 'checked="checked"' : '') .'> Manage';

        $retval .='    </div>'
                 .'    <div class="clearfix"></div>'
                 .'</div>';

        $new_parent = $s->id;
        if ( ! empty ($s->childs) AND $depth <= 30) {  // max 30 leaves
            $new_depth = $depth + 1;
            $retval .= getChild($s->childs, $role, $new_depth)
                   .'</li>';
        } else {
            $retval .= '</li>';
        }
    }

    return $retval.'</ul>';
}
?>
<style>
    .prow:hover {
        background: #eee;
    }
</style>
<div class="page-header">
    <h2>Tambah Hak Akses</h2>
    <div class="clearfix"></div>
</div>

<?php
if (empty ($roles_edit->id)) {
    echo form_open(site_url ('admin/roles_save'), 'id="fRoles"');
} else {
    echo form_open(site_url ('admin/roles_update'), 'id="fRoles"');
    echo form_hidden('id', $roles_edit->id);
}
?>
<div class="row-fluid form-horizontal">
    <div class="span6">
        <div class="control-group">
            <label class="control-label" for="title">Nama</label>
            <div class="controls">
                <?php echo form_input('role_name', $role_name); ?>
            </div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">
            <label class="control-label" for="name">Catatan</label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'notes', 'value'=>$role_notes,'class'=>'span12', 'style'=>'height:60px')); ?>
            </div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <?php
        if ( ! empty ($roles_edit->id)) {
            $html_role =  getChild($structure_tree, $role);
            if ($html_role == '<ul></ul>') {
                echo 'Silakan buat struktur terlebih dahulu, selanjutnya Anda dapat menentukan hak aksesnya disini.';
            } else {
                echo $html_role;
            }
         }
        ?>
        <div class="well">
            <button type="submit" class="btn btn-primary pull-right"><span>Simpan</span></button>
            <div class="clearfix"></div>
        </div>
        <small>Untuk struktur yang dibuat setelah menyimpan hak akses ini, maka default Role (Hak akses) pada struktur tersebut adalah NOACCESS.</small>
    </div>
</div>
<?php echo form_close(); ?>

<script>
    var dedeh;

    $(function() {
        var nval;
        $('.role-detail').click(function() {
            nval = $(this).val();
            var uli = $(this).parent().parent().parent().find('ul li');
            uli.each(function() {
                $(this).find('input').filter('[value="' + nval + '"]').attr('checked', true);
            });
        });

        $('#fRoles').submit(function (e) {
            //e.preventDefault();
        });
    });
</script>

<?php
include 'footer.php';
