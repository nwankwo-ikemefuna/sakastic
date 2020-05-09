<?php
defined('BASEPATH') or die('Direct access not allowed');

class Posts extends Core_controller {
    public function __construct() {
        parent::__construct();
        //controller library
        $params = ['type' => 'post'];
        $this->load->library('controllers/post_lib', $params);
        $this->page_scripts = ['web/js/posts'];
    }


    public function index() {
        //take me home
        redirect('');
    }


    public function view($id) {
        $this->show_page_title = false;
        $row = $this->post_model->get_details('post', $id, 'id', ['all']);
        //does post even exist?
        if ( ! $row) redirect(''); //go home
        $page_title = word_limiter(strip_tags($row->content), 20);
        $this->web_header($page_title);
        $data['id'] = $id;
        $this->load->view('posts/view', $data);
        $this->web_footer('post_view');
    }


    public function view_ajax($id) {
        $this->post_lib->view_ajax($id);
    }


    public function edit_ajax($id) {
        $this->post_lib->edit_ajax($id);
    }

}