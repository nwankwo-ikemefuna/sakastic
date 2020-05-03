<?php
defined('BASEPATH') or die('Direct access not allowed');

class Posts extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->page_scripts = ['posts'];
    }


    public function index() {
        //take me home
        redirect('');
    }


    public function view_ajax($id) {
        $data['id'] = $id;
        $data['row'] = $this->post_model->get_details($id, "p.content");
        $this->load->view('posts/view', $data);
    }


    public function edit_ajax($id) {
        $data['id'] = $id;
        $data['row'] = $this->post_model->get_details($id, "p.content");
        $this->load->view('posts/edit', $data);
    }

}