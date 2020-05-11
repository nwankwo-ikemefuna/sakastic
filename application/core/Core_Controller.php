<?php

class Core_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();

		//constants
		require_once 'application/config/http_codes.php';
		require_once 'application/config/consts.php';
		$this->site_author = 'SoftBytech';
		$this->site_author_url = 'https://softbytech.com';
        //trashed?
		$this->trashed = trashed_record_list();
		//company details
		$this->site_name = SITE_NAME;
		$this->site_description = SITE_DESCRIPTION;
		//get current controller class 
		$this->c_controller = $this->router->fetch_class();
		$this->c_method = $this->router->fetch_method();
		//current page
		$this->page = $this->c_method;
		//page scripts
		$this->page_scripts = [];
		//module
		$this->module = '';
		//table
		$this->table = ''; //required esp for bulk action
		//max data 
		$this->max_data = '';
		//crud buttons
		$this->butts = [];
		//bulk action options
		$this->ba_opts = [];
		$this->show_sidebar = true;
		$this->show_bcrumbs = true;
		$this->show_page_title = false;
		$this->show_disclaimer = false;
		$this->bcrumbs = [];
	}


	protected function web_header($page_title, $current_page = '') {
		$data['page_title'] = $page_title;
		$data['current_page'] = $current_page;
		return $this->load->view('web/layout/header', $data);
	}
	

	protected function web_footer($current_page = '') {
		$data['current_page'] = $current_page;
		return $this->load->view('web/layout/footer', $data);
	}
	
	
	protected function portal_header($page_title, $meta_tags = '') {
		//update requested page to user if it's portal
		if ($this->session->ajax_requested_page == base_url('portal')) {
			$this->session->ajax_requested_page = base_url('user');
		} 
		$data['page_title'] = $page_title;
		$data['meta_tags'] = $meta_tags;
		return $this->load->view('portal/layout/header', $data);
	}
	

	protected function portal_footer($current_page = '') {
		$data['current_page'] = $current_page;
		return $this->load->view('portal/layout/footer', $data);
	}


	protected function ajax_header($page_title, $record_count = '', $crud_rec_id = '', $max_data = '') {
		$this->auth->ajax_request_restricted();
		$this->session->ajax_requested_page = get_requested_page();
		$data['page_title'] = $page_title;
		$data['record_count'] = $record_count;
		$data['crud_rec_id'] = $crud_rec_id;
		$data['max_data'] = $max_data;
		return $this->load->view('portal/layout/ajax_header', $data);
	}
	

	protected function ajax_footer($current_page = '') {
		$data['current_page'] = $current_page;
		return $this->load->view('portal/layout/ajax_footer', $data);
	}


	public function unique_data($table, $field, $is_edit = false, $edit_id = '', $where = []) {
		$found = $this->common_model->get_unique_row($table, $field, $is_edit, $edit_id, $where);
		if ( ! $found) return TRUE;
		json_response(xpost($field).' already exists!', false);
	}


	protected function check_data($table, $param, $where = [], $column = 'id', $redirect = 'error_404') { 
		$found = $this->common_model->get_row($table, $param, $column, 0, [], '', $where);
		if ($found) return TRUE;
		$page = get_requested_page();
		$this->session->set_flashdata('error_msg', "The resource you tried to access at <b>{$page}</b> was not found. It may not exist, have been deleted, or you may not have permission to view it.");
		$redirect .= '?page='.$page;
		redirect($redirect);
    }


    public function check_loggedin() {
        if ($this->session->user_loggedin) return true;
        json_response('You are not logged in!', false);
    }


    public function check_userdata($user_id) {
        if ($user_id == $this->session->user_id) return true;
        json_response('Not allowed', false);
    }


    public function check_pass_strength() {
        $password = xpost('password');
        $check_pass = password_strength($password);
        //password cool...
        if ( ! $check_pass['has_err'] ) return true;
        $this->form_validation->set_message('check_pass_strength', $check_pass['err']);
        return false;
    }


    public function disallowed_usernames() {
        $username = xpost('username');
        $disallowed = ['sakastic', 'sakastic_admin', 'sakasticadmin', 'admin', 'super_admin', 'superadmin', 'lord', 'god', 'penis', 'pussy', 'vagina', 'fuck', 'fucker', 'boobs', 'justboobs', 'cunt', 'bitch', 'bullshit', 'motherfucker'];
        if ( ! in_array(strtolower($username), $disallowed)) return true;
        $this->form_validation->set_message('disallowed_usernames', "Why would you use <b>{$username}</b> as username? Please choose a different one.");
        return false;
    }
	
}