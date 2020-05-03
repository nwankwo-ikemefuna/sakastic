<?php
defined('BASEPATH') or die('Direct access not allowed');

class Account extends Core_controller {
    public function __construct() {
        parent::__construct();
    }


    public function register() {
        $this->show_sidebar = false;
        $this->show_page_title = false;
        $this->web_header('Sign Up');
        $this->load->view('auth/register');
        $this->web_footer();
    }


    public function login() {
        $this->show_sidebar = false;
        $this->show_page_title = false;
        $this->web_header('Login');
        $data['vtype'] = 'regular';
        $this->load->view('auth/login', $data);
        $this->web_footer();
    }


    public function forgot_pass() {
        $this->show_sidebar = false;
        $this->show_page_title = false;
        $this->web_header('Password Recovery');
        $this->load->view('auth/forgot_pass');
        $this->web_footer();
    }


    public function reset_pass($username, $reset_code) {
        $this->show_sidebar = false;
        $this->show_page_title = false;
        $this->web_header('Password Reset');
        $data['username'] = $username;
        $data['reset_code'] = $reset_code;
        $this->load->view('auth/reset_pass', $data);
        $this->web_footer();
    }


    public function logout() {
        $this->auth->logout('login');
    }
    
}