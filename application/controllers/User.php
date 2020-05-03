<?php
defined('BASEPATH') or die('Direct access not allowed');

class User extends Core_controller {
	public function __construct() {
		parent::__construct();
		$this->auth->login_restricted();
	}

	
	public function index() { 
		$view = 'portal/' . ( company_user() ? 'company' : 'customer') . '/index';
		if ($this->session->user_password_set != 1) {
			$reset_url = ajax_page_link('user/reset_pass', 'Reset it now', 'underline-link');
			$this->session->set_flashdata('error_msg', 'You have not reset your default password. '.$reset_url);
		}
		$this->ajax_header('Dashboard');
		$this->load->view($view); 
		$this->ajax_footer();
	}


	public function reset_pass() { 
		//buttons
		$this->butts = ['save' => ['form' => 'reset_pass_form']];
		$this->ajax_header('Reset Password');
		$this->load->view('portal/account/reset_pass');
		$this->ajax_footer();
	}


	public function reset_pass_ajax() { 
        $this->form_validation->set_rules('curr_password', 'Password', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|callback_check_pass_strength');
        $this->form_validation->set_rules('c_password', 'Confirm Password', 'trim|required|matches[password]', ['matches'   => 'Passwords do not match']);
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        //is current password correct?
        if ( ! password_verify(xpost('curr_password'), $this->session->user_password))
        	json_response('Current password not correct', false);
       	//is current password same as new password
       	if (xpost('curr_password') == xpost('password'))
        	json_response('New password cannot be same as current password', false);
        $data = [
        	'password' => password_hash(xpost('password'), PASSWORD_DEFAULT), 
        	'password_set' => 1
        ];
        $this->common_model->update(T_USERS, $data, ['id' => $this->session->user_id]);
        json_response(['redirect' => 'user']);
    }

}