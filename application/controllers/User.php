<?php
defined('BASEPATH') or die('Direct access not allowed');

class User extends Core_controller {
	public function __construct() {
		parent::__construct();
		$this->auth->login_restricted();
		$this->usergroup = $this->session->user_usergroup; 
		$this->is_admin = ($this->usergroup == ADMIN);

		$this->user_details = $this->user_model->get_details($this->session->user_id, 'id', ['all']);
		// last_sql(); die;
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


	protected function dash_header($page_title, $column = '') {
		$this->session->ajax_requested_page = get_requested_page();
		$data['page_title'] = $page_title;
		$data['column'] = $column;
		return $this->load->view('user/layout/dash_header', $data);
	}
	

	protected function dash_footer($current_page = '') {
		$data['current_page'] = $current_page;
		return $this->load->view('user/layout/dash_footer', $data);
	}


	public function dashboard() { 
		$this->page_scripts = ['web/js/posts', 'user/js/user'];
		$this->show_sidebar = false;
		$data['row'] = $this->user_details;
		$data['is_me'] = $this->is_me;
		$this->web_header('My Dashboard');
		$this->load->view('user/index', $data); 
		$this->web_footer('dash');
	}


	public function dash() { 
		$data['row'] = $this->user_details;
		$data['is_me'] = $this->is_me;
		$this->dash_header('');
		$this->load->view('user/dash_ajax', $data);
		$this->dash_footer();
	}


	public function profile() { 
		$data['row'] = $this->user_details;
		$this->dash_header('My Profile', '8?2');
		$this->load->view('user/profile_ajax', $data);
		$this->dash_footer();
	}


	public function edit() { 
		$data['row'] = $this->user_details;
		$data['countries'] = $this->common_model->get_countries('id, name');
		$this->dash_header('Edit Profile', '8?2');
		$this->load->view('user/edit_ajax', $data);
		$this->dash_footer();
	}


	public function change_avatar() { 
		$data['avatar'] = $this->user_details->avatar;
		$this->dash_header('Change Avatar', '6?3');
		$this->load->view('user/change_avatar_ajax', $data);
		$this->dash_footer();
	}


	public function reset_account() { 
		$data['row'] = $this->user_details;
		$this->dash_header('Account Settings', '6?3');
		$this->load->view('user/reset_account_ajax', $data);
		$this->dash_footer();
	}

}