<?php
defined('BASEPATH') or die('Direct access not allowed');

class Comments extends Core_controller {
    public function __construct() {
        parent::__construct();
        //controller library
        $params = ['type' => 'comment'];
        $this->load->library('controllers/post_lib', $params);
    }


    public function index() {
        //take me home
        redirect('');
    }


    public function list_ajax($post_id, $comment_id) {
        $data['post_id'] = $post_id;
        $data['comment_id'] = $comment_id;
        $data['pc_id'] = $post_id.'_'.$comment_id;
        $data['type'] = 'comment';
        $data['reply_type'] = $comment_id == 0 ? 'comment' : 'reply';
        $this->load->view('posts/comments', $data);
    }


    public function view_ajax($id) {
        $this->post_lib->view_ajax($id);
    }


    public function edit_ajax($id) {
        $this->post_lib->edit_ajax($id);
    }

}