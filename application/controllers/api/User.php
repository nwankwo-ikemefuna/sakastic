<?php
defined('BASEPATH') or die('Direct access not allowed');

class User extends Core_controller {
	public function __construct() {
		parent::__construct();
		$this->auth->login_restricted();
		$this->usergroup = $this->session->user_usergroup; 
		$this->is_admin = ($this->usergroup == ADMIN);

		$this->user_details = $this->user_model->get_details($this->session->user_id, 'id', ['all']);
	}

	public function edit() { 
		$this->form_validation->set_rules('sex', 'Sex', 'trim|required');
		$this->form_validation->set_rules('country', 'Country', 'trim|required');
		$this->form_validation->set_rules('quote', 'Quote', 'trim');
		// $social_err = ['alpha_dash' => 'Only your %s username is required!'];
		$this->form_validation->set_rules('social_facebook', 'Facebook', 'trim');
		$this->form_validation->set_rules('social_twitter', 'Twitter', 'trim');
		$this->form_validation->set_rules('social_instagram', 'Instagram', 'trim');
		$this->form_validation->set_rules('social_linkedin', 'Linkedin', 'trim');
		if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $this->user_model->edit($this->session->user_id);
        json_response(['redirect' => 'user/profile']);
    }


	public function reset_account() { 
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		if (xpost('email') != $this->session->user_email) {
			$this->form_validation->set_rules('email', 'Email', 'is_unique['.T_USERS.'.email]', ['is_unique' => 'Email is taken']);
		}
        $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[3]|max_length[15]|callback_disallowed_usernames');
        if (xpost('username') != $this->session->user_username) {
			$this->form_validation->set_rules('username', 'Username', 'is_unique['.T_USERS.'.username]', ['is_unique' => 'Username not available']);
		}
		if (xpost('change_pass') == 1) {
	        $this->form_validation->set_rules('curr_password', 'Password', 'trim|required');
	        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
	        $this->form_validation->set_rules('c_password', 'Confirm Password', 'trim|required|matches[password]', ['matches'   => 'Passwords do not match']);
	    }
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $data = [
        	'email' => xpost('email'),
        	'username' => xpost('username')
        ];
		if (xpost('change_pass') == 1) {
	        //is current password correct?
	        if ( ! password_verify(xpost('curr_password'), $this->session->user_password))
	        	json_response('Current password not correct', false);
	       	//is current password same as new password
	       	if (xpost('curr_password') == xpost('password'))
	        	json_response('New password cannot be same as current password', false);
	        $data = array_merge($data, [
	        	'password' => password_hash(xpost('password'), PASSWORD_DEFAULT), 
	        	'password_set' => 1
	        ]);
	    }
        $this->common_model->update(T_USERS, $data, ['id' => $this->session->user_id]);
        //log user out
        json_response(['redirect' => 'logout']);
    }


    public function change_avatar() {
        $path = 'uploads/images/users';
        $conf = [
        	'path' => $path, 
        	'ext' => 'jpg|jpeg|png', 
        	'size' => 100, 
        	'resize' => true, 
        	'resize_width' => 100, 
        	'resize_height' => 100,
        	'delete_origin' => true, //delete original 
        	'required' => true
        ];
        $upload = upload_file('photo', $conf);
        // pretty_print($upload); die;
        //file upload fails
        if ( ! $upload['status']) 
            json_response($upload['error'], false);
        //delete current avatar
        //we can't rely on the avatar saved in session, so we make a fresh query to fetch the avatar
        $avatar = $this->user_model->get_details($this->session->user_id, 'id', [], 'photo')->photo;
        unlink_file($path, $avatar);
        //update new
        $this->common_model->update(T_USERS, ['photo' => $upload['file_name']], ['id' => $this->session->user_id]);
        //reload page
        json_response(['redirect' => 'user/change_avatar']);
    }


}