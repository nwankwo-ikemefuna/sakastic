<?php
$attrs = ['id' => 'reset_account_form', 'class' => 'ajax_form', 'data-type' => 'redirect', 'data-redirect' => base_url('logout')];
xform_open('api/user/reset_account', $attrs);
	xform_group_list('Email', 'email', 'email', $row->email, true);
	xform_group_list('Username', 'username', 'text', $row->username, true);
	xform_check('Change Password', 'change_pass', 'checkbox', 'change_pass', 1, false, false, false, ['gclass' => 'mt-3']); ?>
	<div id="change_pass_section" style="display: none;">
		<?php
		xform_group_list('Current Password', 'curr_password', 'password');
		xform_group_list('New Password', 'password', 'password');
		xform_group_list('Confirm New Password', 'c_password', 'password'); ?>
	</div>
	<?php
	xform_notice();
	xform_submit('Submit', '', ['class' => 'btn-primary btn_raised_sm']);
xform_close();
?>