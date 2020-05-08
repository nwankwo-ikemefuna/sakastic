<?php
defined('BASEPATH') or die('Direct access not allowed');

class Posts extends Core_controller {
    public function __construct() {
        parent::__construct();
        //controller library
        $this->img_upload_path = 'uploads/images/posts';
        $params = ['type' => 'post', 'upload_path' => $this->img_upload_path];
        $this->load->library('controllers/post_lib', $params);
    }


    public function list($page = 0) {
        $filter = $this->post_model->filter();
        $this->post_lib->list($page, $filter['where'], $filter['order'], $filter['count_joins']);
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


    public function follow() {
        $this->post_lib->follow();
    }


    public function delete() {
        $this->post_lib->delete();
    }


    private function post_type($where, $order, $having = '', $limit = 5) {
        $select = "p.id, p.content, p.votes, p.date_created ## avatar, username, comment_count";
        $data = $this->post_model->get_record_list('post', ['u', 'c'], $select, $where ,$order, $limit, 0, $having, true);
        json_response($data);
    }


    public function recent() {
        $this->post_type([], ['p.date_created' => 'desc']);
    }


    public function trending() {
        $query = $this->post_model->trending_query();
        $this->post_type($query['where'], $query['order'], $query['having']);
    }


    public function followed() {
        $query = $this->post_model->followed_query();
        $this->post_type($query['where'], ['p.date_created' => 'desc']);
    }

}