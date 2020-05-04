<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

	private $ci;
	private $license_enabled = TRUE;
	private $token_enabled = TRUE;

	public function __construct() {
		$this->ci =& get_instance();
	}


	public function login($username_key, $success_msg = 'Login successful') {
		$this->ci->form_validation->set_rules($username_key, ucfirst($username_key), 'trim|required');
        $this->ci->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->ci->form_validation->run() === FALSE) 
            json_response(validation_errors(), false);    

        $username = xpost($username_key);
        $password = xpost('password');
        $row = $this->ci->user_model->get_details($username, $username_key, [], "id, password");
		//user exists, is not trashed, and password is correct...
        if ($row && password_verify($password, $row->password)) {
        	$this->ci->session->set_userdata($this->login_data($row->id));
        	$this->set_requested_page();
        	json_response($success_msg);
        }
        json_response(ucwords($username_key) . ' or password not correct', false);
    }


    public function login_data($id) {
    	$row = $this->ci->user_model->get_details($id, 'id', ['all']);
		$fields = $tables = $this->ci->db->list_fields(T_USERS);
		$data = [];
		if ( ! $row) return;
		$exclude = [];
		//create keys from column names with prefix: user_
		foreach ($fields as $field) {
			//exclude us please
			if (in_array($field, $exclude)) continue;
			$field_key = 'user_' . $field;
			$data[$field_key] = $row->$field;
		}
		//others
		$data = array_merge($data, [
			'user_loggedin' => TRUE
		]);
		return $data;
    } 


    public function logout($redirect = 'login') {
    	$data = array_keys($this->login_data($this->ci->session->user_id));
    	$this->ci->session->unset_userdata($data);
    	redirect($redirect);
    }


	public function is_logged_in($redirect = 'portal', $msg = 'You are already logged in!') {
    	if ($this->ci->session->user_loggedin) {
            $this->ci->session->set_flashdata('info_msg', $msg);
            redirect($redirect);
        }
    }


    private function set_requested_page() {
    	$this->ci->session->set_userdata('ajax_requested_page', base_url('user'));
	}


    private function update_requested_page() {
		//create a session to hold the current requested page
		$this->ci->session->set_userdata('ajax_requested_page', get_requested_page());
	}


	public function ajax_request_restricted() {
		//requested page via ajax?
		if ( ! $this->ci->input->is_ajax_request()) {
			//update requested page
			redirect('portal');
		}
	}


	/**
	* Restrict access to pages requiring user to be logged in
	* redirect to login page if user is not logged
	* @return boolean
	*/
	public function login_restricted($usergroup = null, $redirect = 'login') {
		//all
		if ($this->ci->session->user_loggedin && ! empty($usergroup)) return TRUE;
		//specific usergroup
		if ($this->ci->session->user_loggedin && user_group($usergroup)) return TRUE;
		//all usergroups
		if ($this->ci->session->user_loggedin && $usergroup === null) return TRUE;
		//create a session to hold the current requested page
		$this->update_requested_page();
		//redirect to login page
		$this->ci->session->set_flashdata('error_msg', 'You are not logged in. Please login to continue.');
		$this->logout($redirect);
	}


	/**
	* Restrict access to pages requiring user to have reset default password
	* redirect to login page if user is not logged
	* @return boolean
	*/
	public function password_restricted($redirect = 'user/reset_pass') {
		//all
		if ($this->ci->session->user_loggedin && $this->ci->session->user_password_set == 1) return TRUE;
		//create a session to hold the current requested page
		$this->update_requested_page();
		//redirect to password reset page
		$this->ci->session->set_flashdata('error_msg', 'You have not reset your default password');
		redirect($redirect);
	}


	/**
	* Restrict access to pages without the right user group and permissions
	* @return boolean
	*/
	public function vet_access($module, $right, $usergroups = null) {
		//level 1 user here?
		if (intval($this->ci->session->user_level) === 1) return true;
		//all usergroups?
		if (in_array($this->ci->session->user_usergroup, ALL_USERS)) return true;
		//user group 
		$group = $this->ci->session->user_usergroup;
		//is usergroup an array? Cast into array if not
		$usergroups = is_array($usergroups) ? $usergroups : (array) $usergroups;
		if ( !empty($usergroups) && ! in_array($this->ci->session->user_usergroup, $usergroups) ) return false;
		
		//user permissions
		$permissions = $this->ci->session->user_permissions;
	    if ( ! strlen($permissions)) return false;
	    //let's work on the permissions
	    // eg 1#1|2|3|4&2#1|2|3|4
	    // module#right1|right2|...&, ++
	    $perms_arr = explode('&', $permissions);
	    if (empty($perms_arr)) return false;
	    $mods_arr = [];
	    foreach ($perms_arr as $perm) {
	        $ex = explode('#', $perm);
	        $mod = intval($ex[0]);
	        $rights = $ex[1];
	        $arr = explode('|', $rights);
	        $mods_arr[$mod] = array_map('intval', $arr);
	    }
	    $modules = array_keys($mods_arr);
	    $grant_access = false;
	    foreach ($modules as $mod) {
	        //user has right to module?
	        if ( $module === $mod && in_array($right, $mods_arr[$mod]) ) {
	            $grant_access = true;
	        }
	    }
	    return $grant_access;
	}

	

	/**
	* Restrict access to pages without the right permissions
	* redirect to forbidden page
	* @return boolean
	*/
	public function module_restricted($module, $right, $usergroups = null, $ajax = false, $redirect = 'forbidden') {
		$grant_access = $this->vet_access($module, $right, $usergroups);
		//var_dump($usergroups);
		if ($grant_access) return TRUE;
		if ($ajax) {
			json_response('Action not allowed! Insufficient permissions!', false);
		} else {
			$this->ci->session->set_flashdata('error_msg', 'You do not have sufficient permissions to perform the action you attempted.');
			redirect($redirect);
		}
	}


}