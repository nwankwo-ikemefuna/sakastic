<?php if ($vtype == 'regular') { ?>
    <div class="row">
        <div class="col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
<?php } ?>

<h4 class="bar_title">Login</h4>
<?php echo flash_message('success_msg'); ?>
<?php echo flash_message('error_msg', 'danger'); ?>

<?php 
$redirect_url = $vtype == 'regular' ? base_url() : '_self';
$attrs = [
    'id' => $vtype.'_login_form', 
    'class' => 'ajax_form', 
    'data-type' => 'redirect', 
    'data-redirect' => $redirect_url, 
    'data-msg' => "Login successful. Redirecting... <p>If you are not automatically redirected, <a href='{$redirect_url}'>click here</a></p>"
];
echo form_open('api/account/login', $attrs);
    xform_group_list('Email', 'email', 'email', '', true, ['id' => $vtype.'_email']);
    xform_group_list('Password', 'password', 'password', '', true, ['id' => $vtype.'_password']);
    xform_notice();
    xform_submit('Login', $attrs['id'], ['class' => 'btn-primary btn-block clickable m-t-20']);
echo form_close();
?>
<div class="form-group mt-3 mb-0">
    <div class="text-center">
        <div>Forgot password? <a href="<?php echo base_url('forgot_pass'); ?>" class="m-l-5"><b>Recover</b></a></div>
        <div>Don't have an account? <a href="<?php echo base_url('register'); ?>" class="m-l-5"><b>Signup</b></a></div>
    </div>
</div>

<?php if ($vtype == 'regular') { ?>
        </div>
    </div>
<?php } ?>