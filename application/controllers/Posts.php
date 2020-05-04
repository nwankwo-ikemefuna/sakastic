<?php
defined('BASEPATH') or die('Direct access not allowed');

class Posts extends Core_controller {
    public function __construct() {
        parent::__construct();
        //controller library
        $params = ['type' => 'post'];
        $this->load->library('controllers/post_lib', $params);
        $this->page_scripts = ['posts'];
    }


    public function index() {
        //take me home
        redirect('');
    }


    public function view_ajax($id) {
        $this->post_lib->view_ajax($id);
    }


    public function edit_ajax($id) {
        $this->post_lib->edit_ajax($id);
    }

}