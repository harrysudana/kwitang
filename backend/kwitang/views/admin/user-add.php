<?php if ( ! defined ('FRONT_PATH')) exit ('Kwitang ERROR..!!!');

include 'header.php';
?>
<div class="page-header">
    <h2><?php echo lang('k_add').' '.lang('k_users'); ?></h2>
</div>

<?php
echo form_open_multipart(site_url ('admin/user_save'), 'class="form-horizontal"');
$roles_dropdown = array();
foreach ($roles as $r) {
    $roles_dropdown[$r->id] = $r->title;
}
?>
<div class="row-fluid">
    <div class="span6">
        <div class="control-group">
            <label class="control-label" for="fullname"><?php echo lang('k_fullname'); ?></label>
            <div class="controls">
                <?php echo form_input('fullname', '', 'id="fullname" class="input-xlarge"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="gender"><?php echo lang('k_gender'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('gender', array('male'=>'Laki-laki', 'female'=>'Perempuan'), 'id="gender" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="birth_date"><?php echo lang('k_birth_date'); ?></label>
            <div class="controls">
                <div class="justdatepicker input-append">
                        <?php echo form_input('birth_date', date('Y-m-d'), 'id="birth_date" class="input-medium"'); ?>
                        <div class="add-on"><i></i></div>
                    </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="address"><?php echo lang('k_address'); ?></label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'address', 'id' => 'address', 'rows'=>'4', 'class'=>'input-xlarge')); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="phone"><?php echo lang('k_phone'); ?></label>
            <div class="controls">
                <?php echo form_input('phone', '', 'id="phone" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="mobile"><?php echo lang('k_mobile'); ?></label>
            <div class="controls">
                <?php echo form_input('mobile', '', 'id="mobile" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email"><?php echo lang('k_email'); ?></label>
            <div class="controls">
                <?php echo form_input('email', '', 'id="email" class="input-large"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="website"><?php echo lang('k_website'); ?></label>
            <div class="controls">
                <?php echo form_input('website', '', 'id="website" class="input-xlarge"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="notes"><?php echo lang('k_notes'); ?></label>
            <div class="controls">
                <?php echo form_textarea(array('name'=>'notes', 'id' => 'notes', 'rows'=>'5', 'class'=>'input-xlarge')); ?>
            </div>
        </div>
    </div>
    <div class="span6">
        <div class="control-group">
            <label class="control-label" for="username"><?php echo lang('k_username'); ?></label>
            <div class="controls">
                <?php echo form_input('username', '', 'id="username" autocomplete="off" class="input-medium" maxlength="45"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?php echo lang('k_password'); ?></label>
            <div class="controls">
                <?php echo form_password('pass', '', 'id="pass" autocomplete="off" class="input-medium"'); ?>
                <br><small>Repeat password:</small><br>
                <?php echo form_password('pass1', '', 'id="pass1" class="input-medium"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="level"><?php echo lang('k_level'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('level', $user_levels, 'AUTHOR', 'id="level" class="input-small"'); ?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="role_id"><?php echo lang('k_role'); ?></label>
            <div class="controls">
                <?php echo form_dropdown('role_id', $roles_dropdown, 'ADMIN', 'id="role_id" class="input-small"'); ?>
                <span id="role_desc"></span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="isactive"><?php echo lang('k_login'); ?></label>
            <div class="controls">
                <?php echo form_checkbox('isactive', 'active', true, 'id="isactive" style="margin:0"'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="span12 well">
        <div class="pull-left"><input type="button" value="<?php echo lang('k_cancel'); ?>" onclick="history.back()" class="btn"></div>
        <div class="pull-right"><?php echo form_submit('btnSubmit', lang('k_save'), 'class="btn btn-primary"'); ?></div>
    </div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#level').change(function() {
            if ( $(this).val() != 'ADMIN') {
                $('#role_id').removeAttr('disabled');
                $('#role_desc').text('');
            } else {
                $('#role_id').attr('disabled', 'disabled');
                $('#role_desc').text('Level Admin tidak dibatasi hak aksesnya.');
            }
        });

        $('#username').keyup(function(e) {
            var text = $(this).val();
            text = text.replace(/[^a-zA-Z0-9-_]+/ig, '');
            $(this).val(text.substring(0, 45));
        });

        $('form').submit(function() {
            var errmsg = '';

            if ( $('#fullname').val().length <= 0) {
                errmsg += 'Silakan mengisi Nama Lengkap\r\n';
            }
            if ( $('#username').val().length <= 0) {
                errmsg += 'Silakan mengisi Username\r\n';
            }
            if ( $('#email').val().length <= 0) {
                errmsg += 'Silakan mengisi Email\r\n';
            }
            if ( $('#pass').val().length <= 0) {
                errmsg += 'Silakan mengisi Password\r\n';
            }
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
