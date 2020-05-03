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
        // last_sql();
        $total_records = $this->post_model->count_all($where);
        // last_sql();
        $data = paginate($records, $total_records, $per_page, 'api/posts/list');
        json_response($data);
    }


    public function vote() {
        $this->check_loggedin();
        $data = $this->post_model->vote();
        json_response($data);
    }


    public function add() {
        $this->check_loggedin();
        $this->summernote->remove_files(xpost('content'), xpost('smt_images'), $this->img_upload_path);
        $data = $this->post_model->add();
        json_response($data);
    }


    public function edit() {
        $this->check_loggedin();
        $id = xpost('id');
        $row = $this->post_model->get_details($id);
        $this->check_userdata($row->user_id);
        $this->summernote->remove_files(xpost('content'), xpost('smt_images'), $this->img_upload_path);
        $data = $this->post_model->edit();
        json_response($data);
    }


    public function delete() {
        $this->check_loggedin();
        $id = xpost('id');
        $row = $this->post_model->get_details($id, "p.user_id, p.can_delete, p.content");
        $this->check_userdata($row->user_id);
        //can they delete?
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