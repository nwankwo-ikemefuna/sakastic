<?php
defined('BASEPATH') or die('Direct access not allowed');

class User extends Core_controller {
	public function __construct() {
		parent::__construct();
		$this->auth->login_restricted();
		$this->usergroup = $this->session->user_usergroup; 
		$this->is_admin = ($this->usergroup == ADMIN);

		$this->user_details = $this->user_model->get_details($this->session->user_id, 'id', ['all']);
		$this->is_me = true;
	}

	
	public function index() { 
		if ($this->session->user_password_set != 1) {
			$reset_url = ajax_page_link('user/reset_pass', 'Reset it now', 'underline-link');
			$this->session->set_flashdata('error_msg', 'You have not reset your default password. '.$reset_url);
		}
		$this->web_header('My Dashboard');
		$this->load->view('user/index'); 
		$this->web_footer();
	}


	protected function dash_header($page_title) {
		$this->session->ajax_requested_page = get_requested_page();
		$data['page_title'] = $page_title;
		return $this->load->view('user/layout/dash_header', $data);
	}
	

	protected function dash_footer($current_page = '') {
		$data['current_page'] = $current_page;
		return $this->load->view('user/layout/dash_footer', $data);
	}


	public function dashboard() { 
		$this->page_scripts = ['posts'];
		$this->show_sidebar = false;
		$data['row'] = $this->user_details;
		$data['is_me'] = $this->is_me;
		$this->web_header('My Dashboard');
		$this->load->view('user/index', $data); 
		$this->web_footer('profile');
	}


	public function dash() { 
		//buttons
		$row = $this->user_model->get_details($this->session->user_username, 'username', ['all']);
		$data['row'] = $this->user_details;
		$data['is_me'] = $this->is_me;
		$this->dash_header('');
		$this->load->view('user/dash_ajax', $data);
		$this->dash_footer();
	}


	public function profile() { 
		$data['row'] = $this->user_details;
		$data['is_me'] = $this->is_me;
		$this->dash_header('My Profile');
		$this->load->view('user/profile_ajax', $data);
		$this->dash_footer();
	}


	public function reset_pass() { 
		//buttons
		$this->butts = ['save' => ['form' => 'reset_pass_form']];
		$this->dash_header('Reset Password');
		$this->load->view('user/reset_pass_ajax');
		$this->dash_footer();
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