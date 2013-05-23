<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <h1><?php echo lang('k_edit').' '.lang('k_users'); ?></h1>
</div>

<?php
    echo form_open_multipart(site_url ('admin/user_update'), 'class="form-horizontal"');
    echo form_hidden('username', $user_edit->username);
    $roles_dropdown = array();
    foreach ($roles as $r) {
        $roles_dropdown[$r->id] = $r->title;
        if ($user_edit->role_id == $r->id) {
            $current_role = $r;
        }
    }
?>
<div class="row-fluid">
    <div class="span6">
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_fullname'); ?></label>
            <div class="controls">
                <?php echo form_input('fullname', $user_edit->fullname ,'id="fullname" class="input-xlarge" data-required="Nama Lengkap"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_gender'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('gender', array('MALE'=>'Laki-laki', 'FEMALE'=>'Perempuan'), $user_edit->gender, 'class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_birth_date'); ?></label>
            <div class="controls">
                <div class="justdatepicker input-append">
                    <?php echo form_input('birth_date', substr($user_edit->birth_date, 0, 10), 'size="19" class="input-medium" data-required="Tanggal Lahir"'); ?>
                    <div class="add-on"><i></i></div>
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="address"><?php echo lang('k_address'); ?></label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'address', 'id' => 'address', 'value'=>$user_edit->address, 'rows'=>'4', 'class'=>'input-xlarge')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="phone"><?php echo lang('k_phone'); ?></label>
            <div class="controls">
                <?php echo form_input('phone', $user_edit->phone, 'id="phone" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="mobile"><?php echo lang('k_mobile'); ?></label>
            <div class="controls">
                <?php echo form_input('mobile', $user_edit->mobile, 'id="mobile" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email"><?php echo lang('k_email'); ?></label>
            <div class="controls">
                <?php echo form_input('email', $user_edit->email, 'id="email" class="input-large" data-required="Email"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="website"><?php echo lang('k_website'); ?></label>
            <div class="controls">
                <?php echo form_input('website', $user_edit->website, 'id="website" class="input-xlarge"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_notes'); ?></label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'notes', 'value'=>$user_edit->notes, 'rows'=>'5', 'class'=>'input-xlarge')); ?>
            </div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">
            <label class="control-label"><?php echo lang('k_username'); ?></label>
            <div class="controls">
                <?php
                if ($current_user->level == 'ADMIN') {
                    echo form_input('new_username', $user_edit->username, 'id="username" autocomplete="off" class="input-medium" maxlength="45"');
                } else {
                    echo '<div style="float:left;padding-top: 5px;"><strong>'.$user_edit->username.'</strong></div>';
                }
                ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_password'); ?></label>
            <div class="controls">
                <?php echo form_password('pass', '', 'autocomplete="off"  id="pass" class="input-medium"'); ?>
                <br><small>Repeat password</small><br>
                <?php echo form_password('pass1', '', 'id="pass1" class="input-medium"'); ?>
                <small class="label label-info">Isi password jika Anda hendak mengubahnya. Jika tidak, biarkan kosong.</small>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="level"><?php echo lang('k_level'); ?></label>
            <div class="controls">
                <?php
                    if ($current_user->level == 'ADMIN') {
                        echo form_dropdown('level', $user_levels, $user_edit->level, 'id="level" class="input-small"');
                    } else {
                        echo '<div style="float:left;padding-top: 5px;"><strong>'.ucfirst ($user_edit->level).'</strong></div>';
                    }
                ?>
            </div>
        </div>
        <?php if ($current_user->level == 'ADMIN') { ?>
        <div class="control-group">
            <label class="control-label" for="role_id"><?php echo lang('k_role'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('role_id', $roles_dropdown, $user_edit->role_id, 'id="role_id" class="input-small"'); ?>
                <span id="role_desc"></span>
            </div>
        </div>
        <?php } ?>
        <div class="control-group">
            <label class="control-label" for="activebadge"><?php echo lang('k_login'); ?></label>
            <div class="controls">
                <?php
                if ($current_user->level == 'ADMIN') {
                    echo form_checkbox('isactive', 'active', ($user_edit->active == 1  ? true: false), 'id="isactive" style="margin:0"');
                } else {
                    echo $user_edit->active ? '<i class="icon-green icon-ok"></i> Login enabled' : '<i class="icon-white icon-remove"></i> Login disabled';
                }
                ?>
            </div>
        </div>

        <hr />
        <?php
            $timezones = user_config($current_user->username, 'timezones', 'UP7');
            $language  = user_config($current_user->username, 'language', 'Indonesia');
            $dst       = user_config($current_user->username, 'dst', 'no');
        ?>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_timezone'); ?></label>
            <div class="controls">
                <?php echo timezone_menu($timezones, null, 'config[timezones]'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_admin_language'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('config[language]', array('indonesia' => 'Indonesia' , 'english' => 'English'), $language); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for=""><?php echo lang('k_dst'); ?></label>
            <div class="controls">
                <input type="radio" name="config[dst]" value="yes"<?php echo $dst=='yes' ? ' checked="checked"' : ''; ?> > Yes
                <input type="radio" name="config[dst]" value="no"<?php echo $dst!='yes' ? ' checked="checked"' : ''; ?> > No
            </div>
        </div>

    </div>
</div>

<div class="row-fluid">
    <div class="span12 well">
        <?php
        if ($current_user->level == 'ADMIN') {
            echo '<input type="button" class="btn" value=" &lArr; Batal " onclick="history.back()">'
                 .form_submit('btnSubmit', ' Simpan dan tutup ', 'class="btn btn-primary pull-right"');
        } else {
            echo form_submit('btnSubmit', ' Simpan ', 'class="btn btn-primary pull-right"');
        }
        ?>
        <div class="clearfix"></div>
    </div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if ($current_user->level == 'ADMIN') { ?>
            cek_level();

            $('#username').keyup(function(e) {
                var text = $(this).val();
                text = text.replace(/[^a-zA-Z0-9-_]+/ig, '');
                $(this).val(text.substring(0, 20));
            });
            $('#level').change(function() {
                cek_level();
            });
            function cek_level() {
                if ( $('#level').val() != 'ADMIN') {
                    $('#role_id').removeAttr('disabled');
                    $('#role_desc').text('');
                } else {
                    $('#role_id').attr('disabled', 'disabled');
                    $('#role_desc').text('Level Admin tidak dibatasi hak aksesnya.');
                }
            }
        <?php } ?>

        $('form').submit(function(e) {
            var errmsg = '';

            if ( $('#fullname').val().length <= 0) {
                errmsg += 'Silakan mengisi Nama Lengkap\r\n';
            }
            if ( $('#email').val().length <= 0) {
                errmsg += 'Silakan mengisi Email\r\n';
            }
            <?php if ($current_user->level == 'ADMIN') { ?>
            if ( $('#username').val().length <= 0) {
                errmsg += 'Silakan mengisi Username\r\n';
            }
            <?php } ?>

            if ( $('#pass').val() !== $('#pass1').val()) {
                errmsg += "Password tidak sama.\r\n";
            }

            if(errmsg !== '') {
                alert(errmsg);

                return false;
            }

            return true;
        });
    });
</script>

<?php
include 'footer.php';
