<?php
defined('BASEPATH') or die('Direct access not allowed');

class Comments extends Core_controller {
    public function __construct() {
        parent::__construct();
        //controller library
        $this->img_upload_path = 'uploads/images/posts';
        $params = ['type' => 'comment', 'upload_path' => $this->img_upload_path];
        $this->load->library('controllers/post_lib', $params);
    }


    public function list($page = 0) {
        $where = [
            'c.post_id' => xpost('post_id'), 
            'c.parent_id' => xpost('comment_id')
        ];
        $this->post_lib->list($where, $page);
    }


    public function view() {
        $this->post_lib->view();
    }


    public function add() {
        $this->post_lib->add();
    }


    public function edit() {
        $this->post_lib->edit();
    }


    public function vote() {
        $this->post_lib->vote();
    }


    public function delete() {
        $this->post_lib->delete();
    }

}