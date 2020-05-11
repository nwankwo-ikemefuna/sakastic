<div class="row">
    <div class="col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">
        <h4 class="bar_title len10">Forgot Password</h4>
        <?php 
        $attrs = [
            'id' => 'forgot_pass_form',
            'class' => 'ajax_form', 
            'data-type' => 'none',
            'data-redirect' => '_void',
            'data-msg' => 'Instructions to reset your password has been sent to your email address'
        ];
        xform_open('api/account/forgot_pass', $attrs);
            xform_group_list('Email', 'email', 'email', '', true, ['id' => 'signup_email']);
            xform_notice();
            xform_submit('Recover', $attrs['id'], ['class' => 'btn-primary btn-block clickable m-t-20']);
        xform_close();
        ?>
        <div class="form-group mt-3 mb-0">
            <div class="text-center">
                <div>Don't have an account? <a href="<?php echo base_url('register'); ?>" class="m-l-5"><b>Signup</b></a></div>
            </div>
        </div>
    </div>
</div>