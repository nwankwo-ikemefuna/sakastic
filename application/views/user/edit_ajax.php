<?php
$attrs = ['id' => 'edit_profile_form', 'class' => 'ajax_form', 'data-type' => 'redirect', 'data-redirect' => '_ajax_dynamic'];
echo form_open('api/user/edit', $attrs); 
	xform_pre_notice();
	xform_label('Sex', '', ['required' => true, 'class' => 'm-r-10']);
	xform_check('Male', 'sex', 'radio', 'radio', SEX_MALE, ($row->sex == SEX_MALE), true, true);
	xform_check('Female', 'sex', 'radio', 'radio', SEX_FEMALE, ($row->sex == SEX_FEMALE), true, true);
	xform_group_list('Country', 'country', 'select', adit_value($row, 'country', C_NIGERIA), true, 
		['options' => $countries, 'text_col' => 'name']
	);
	xform_group_list('Facebook', 'social_facebook', 'text', $row->social_facebook, false, ['placeholder' => 'my-facebook-username', 'help' => 'https://facebook.com/ will be inserted automatically for you'], [], [], ['prepend' => _social_icon('facebook'), 'pp_class' => 'bg-facebook']);
	xform_group_list('Twitter', 'social_twitter', 'text', $row->social_twitter, false, ['placeholder' => 'my-twitter-username', 'help' => 'https://twitter.com/ will be inserted automatically for you'], [], [], ['prepend' => _social_icon('twitter'), 'pp_class' => 'bg-twitter']);
	xform_group_list('Instagram', 'social_instagram', 'text', $row->social_instagram, false, ['placeholder' => 'my-instagram-ID', 'help' => 'https://instagram.com/ will be inserted automatically for you'], [], [], ['prepend' => _social_icon('instagram'), 'pp_class' => 'bg-instagram']);
	xform_group_list('LinkedIn', 'social_linkedin', 'text', $row->social_linkedin, false, ['placeholder' => 'my-linkedin-ID', 'help' => 'https://linkedin.com/in/ will be inserted automatically for you'], [], [], ['prepend' => _social_icon('linkedin'), 'pp_class' => 'bg-linkedin']);
	xform_group_list('Saka Quote', 'quote', 'textarea', $row->quote, false, ['rows' => '2']);
	xform_notice();
	xform_submit('Submit', '', ['class' => 'btn-primary']);
echo form_close();

function _social_icon($icon) {
	return '<i class="fa fa-'.$icon.'"></i>';
}