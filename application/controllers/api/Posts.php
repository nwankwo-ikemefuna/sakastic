<?php
defined('BASEPATH') or die('Direct access not allowed');

class Posts extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->img_upload_path = 'uploads/images/posts';
    }


    public function filter() {
        $where = [];
        //user posts?
        $username = xpost('user');
        if (strlen($username)) {
            //does the user even exists?
            $sql = $this->user_model->sql([], "u.id");
            $row = $this->user_model->get_row($sql['table'], $username, 'username', -1, $sql['joins'], $sql['select']);
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
        $per_page = 3;
        $page = paginate_offset($page, $per_page);
        $where = $this->filter();
        $records = $this->post_model->get_posts($where, '', 0, $per_page, $page);
        // last_sql(); die;
        $total_records = $this->post_model->count_all($where);
        $data = paginate($records, $total_records, $per_page, 'api/posts/list');
        json_response($data);
    }


    public function view() {
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $id = xpost('id');
        $row = $this->post_model->get_details($id);
        //send prepared post
        $data = $this->post_model->prepare_post($row);
        json_response($data);
    }

    
    public function add() {
        $this->check_loggedin();
        $this->form_validation->set_rules('content', 'Post', 'trim|required');
        $this->form_validation->set_rules('smt_images', 'Images', 'trim');
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $this->summernote->remove_files(xpost('content'), xpost('smt_images'), $this->img_upload_path);
        $data = $this->post_model->add();
        json_response($data);
    }


    public function edit() {
        $this->check_loggedin();
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_rules('content', 'Post', 'trim|required');
        $this->form_validation->set_rules('smt_images', 'Images', 'trim');
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $id = xpost('id');
        $row = $this->post_model->get_details($id);
        //check if user post
        $this->check_userdata($row->user_id);
        $this->summernote->remove_files(xpost('content'), xpost('smt_images'), $this->img_upload_path);
        $data = $this->post_model->edit();
        json_response($data);
    }


    public function vote() {
        $this->check_loggedin();
        //ok, if poster, deny action
        $id = xpost('id');
        $row = $this->post_model->get_details($id);
        if ($row->user_id == $this->session->user_id)
            json_response('Cannot vote own post!', false);
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $data = $this->post_model->vote();
        json_response($data);
    }


    public function delete() {
        $this->check_loggedin();
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        if ($this->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $id = xpost('id');
        $row = $this->post_model->get_details($id, "p.user_id, p.can_delete, p.content");
        //check if user post
        $this->check_userdata($row->user_id);
        //can i delete?
        if (!$row->can_delete)
            json_response('Action denied!', false);
        //delete post
        $content = $row->content;
        $deleted = $this->common_model->delete(T_POSTS, ['id' => $id]);
        if ($deleted) {
            //delete all comments
            $this->common_model->delete(T_COMMENTS, ['post_id' => $id]);
            //remove associated images
            $extracted = $this->summernote->extract($content);
            unlink_files($this->img_upload_path, $extracted);
        }
        json_response('Successful!');
    }

}