<?php if ($vtype == 'regular') { ?>
    <div class="row">
        <div class="col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
            <h4 class="bar_title">Sign Up</h4>
            <?php echo flash_message('success_msg'); ?>
            <?php echo flash_message('error_msg', 'danger'); ?>
<?php } else { ?>
    <h4>Sign Up</h4>
<?php } ?>
<div class="text-muted">Sign up in 10 seconds or less.</div>

<?php 
$redirect_url = $vtype == 'regular' ? base_url() : '_self';
$attrs = [
    'id' => $vtype.'_signup_form', 
    'class' => 'ajax_form', 
    'data-type' => 'redirect', 
    'data-redirect' => $redirect_url, 
    'data-msg' => "Registration successful. Redirecting... <p>If you are not automatically redirected, <a href='{$redirect_url}'>click here</a></p>"
];
xform_open('api/account/register', $attrs);
    xform_group_list('Email', 'email', 'email', '', true, ['id' => $vtype.'_email']);
    xform_group_list('Username', 'username', 'text', '', true, ['id' => $vtype.'_signup_username']);
    xform_group_list('Password', 'password', 'password', '', true, ['id' => $vtype.'_password']);
    xform_group_list('Confirm Password', 'c_password', 'password', '', true, ['id' => $vtype.'_signup_c_password']);
    xform_notice();
    xform_submit('Sign Up', $attrs['id'], ['class' => 'btn-primary btn-block clickable m-t-20']);
xform_close();
?>
<div class="form-group mt-3 mb-0">
    <div class="text-center">
        <div>
            Already have an account? 
            <?php if ($vtype == 'regular') { ?>
                <a href="<?php echo base_url('login'); ?>" class="no_deco m-l-5"><b>Login</b></a>
            <?php } else { ?>
                <a class="acc_form_toggle clickable text-primary m-l-5" data-id="floating_login"><b>Login</b></a>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ($vtype == 'regular') { ?>
        </div>
    </div>
<?php } ?>