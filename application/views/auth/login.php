<?php if ($vtype == 'regular') { ?>
    <div class="row">
        <div class="col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
            <h4 class="bar_title">Login</h4>
            <?php echo flash_message('success_msg'); ?>
            <?php echo flash_message('error_msg', 'danger'); ?>
<?php } else { ?>
    <h4>Login</h4>
<?php } ?>

<?php 
$redirect_url = $vtype == 'regular' ? base_url() : '_self';
$attrs = [
    'id' => $vtype.'_login_form', 
    'class' => 'ajax_form', 
    'data-type' => 'redirect', 
    'data-redirect' => $redirect_url, 
    'data-msg' => "Login successful. Redirecting... <p>If you are not automatically redirected, <a href='{$redirect_url}'>click here</a></p>"
];
xform_open('api/account/login', $attrs);
    xform_group_list('Email', 'email', 'email', '', true, ['id' => $vtype.'_login_email']);
    xform_group_list('Password', 'password', 'password', '', true, ['id' => $vtype.'_login_password']);
    xform_notice();
    xform_submit('Login', $attrs['id'], ['class' => 'btn-primary btn-block clickable m-t-20']);
xform_close();
?>
<div class="form-group mt-3 mb-0">
    <div class="text-center">
        <div>Forgot password? <a href="<?php echo base_url('forgot_pass'); ?>" class="no_deco m-l-5"><b>Recover</b></a></div>
        <div>
            Don't have an account? 
            <?php if ($vtype == 'regular') { ?>
                Don't have an account? <a href="<?php echo base_url('register'); ?>" class="no_deco m-l-5"><b>Signup</b></a>
            <?php } else { ?>
                <a class="acc_form_toggle clickable text-primary m-l-5" data-id="floating_signup"><b>Signup</b></a>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ($vtype == 'regular') { ?>
        </div>
    </div>
<?php } ?>