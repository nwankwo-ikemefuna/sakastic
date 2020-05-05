<div class="row">
    <div class="col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
        <h4 class="bar_title">Sign Up</h4>
        <?php 
        $redirect_url = base_url('login');
        $attrs = [
            'id' => 'signup_form', 
            'class' => 'ajax_form', 
            'data-type' => 'redirect', 
            'data-redirect' => $redirect_url, 
            'data-msg' => "Registration successful. Redirecting... <p>If you are not automatically redirected, <a href='{$redirect_url}'>click here</a></p>"
        ];
        echo form_open('api/account/register', $attrs);
            xform_group_list('Email', 'email', 'email', '', true, ['id' => 'signup_email']);
            xform_group_list('Username', 'username', 'text', '', true, ['id' => 'signup_username']);
            xform_group_list('Password', 'password', 'password', '', true, ['id' => 'signup_password']);
            xform_group_list('Confirm Password', 'c_password', 'password', '', true, ['id' => 'signup_c_password']);
            xform_notice();
            xform_submit('Sign Up', $attrs['id'], ['class' => 'btn-primary btn-block clickable m-t-20']);
        echo form_close();
        ?>
        <div class="form-group mt-3 mb-0">
            <div class="text-center">
                <div>Already have an account? <a href="<?php echo base_url('login'); ?>" class="m-l-5"><b>Login</b></a></div>
            </div>
        </div>
    </div>
</div>