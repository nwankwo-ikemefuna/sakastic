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


    private function filter() {
        $where = [];
        //user posts?
        $username = xpost('user');
        if (strlen($username)) {
            //does the user even exists?
            $row = $this->user_model->get_details($username, 'username', [], "id");
            if ($row) {
                $where = ['p.user_id' => $row->id];
            }
        }
        //searching?
        if (strlen(xpost('search'))) {
            $this_where = $this->post_model->search();
            $where = array_merge($where, [$this_where => null]);
        } 
        return $where;
    }


    public function list($page = 0) {
        $where = $this->filter();
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


    private function post_type($where, $order, $having = '') {
        $limit = 5;
        $select = "p.id, p.content, p.votes, p.date_created ## avatar, username, comment_count";
        $data = $this->post_model->get_record_list('post', ['u', 'c'], $select, $where ,$order, $limit, 0, $having);
        json_response($data);
    }


    public function recent() {
        $this->post_type([], ['p.date_created' => 'desc']);
    }


    public function trending() {
        //this week. skip if no comment
        $where = ['YEARWEEK(p.date_created) = YEARWEEK(NOW())' => ''];
        $this->post_type($where, ['COUNT(c.post_id)' => 'desc'], 'COUNT(c.post_id) > 0');
    }

}