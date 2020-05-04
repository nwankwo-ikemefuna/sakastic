<?php
defined('BASEPATH') or die('Direct access not allowed');

class Post_lib {

    private $ci;

    public function __construct($params) {
        $this->ci =& get_instance();
        $this->type = $params['type'];
        $this->table = $this->type.'s'; //post OR comment
        $this->alias = $this->type[0]; //p OR c
        $this->table_with_alias = $this->table.' '.$this->alias; 
        $this->view_dir = ($this->type == 'post') ? 'posts' : 'posts/comments';
        $this->img_upload_path = $params['upload_path'] ?? '';
    }


    private function filter() {
        $where = [];
        //user posts?
        $username = xpost('user');
        if (strlen($username)) {
            //does the user even exists?
            $row = $this->ci->user_model->get_details($username, 'username', [], "id");
            if ($row) {
                $where = ['p.user_id' => $row->id];
            }
        }
        //searching?
        if (strlen(xpost('search'))) {
            $this_where = $this->ci->post_model->search();
            $where = array_merge($where, [$this_where => null]);
        } 
        return $where;
    }


    public function list($page) {
        $per_page = 3;
        $page = paginate_offset($page, $per_page);
        $where = $this->filter();
        $records = $this->ci->post_model->get_record_list($this->type, ['all'], '*', $where, $per_page, $page);
        $total_records = $this->ci->common_model->count_rows($this->table_with_alias, $where);
        $data = paginate($records, $total_records, $per_page, "api/{$this->type}s/list");
        json_response($data);
    }


    public function view() {
        $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->ci->form_validation->set_rules('type', 'Type', 'trim|required');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $id = xpost('id');
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', ['all']);
        //send prepared post
        $data = $this->ci->post_model->prepare_post($row);
        json_response($data);
    }


    private function adit($crud_type) {
        $this->ci->check_loggedin();
        if ($crud_type == 'edit') {
            $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        }
        $this->ci->form_validation->set_rules('content', 'Post', 'trim|required');
        $this->ci->form_validation->set_rules('smt_images', 'Images', 'trim');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $this->ci->summernote->remove_files(xpost('content'), xpost('smt_images'), $this->img_upload_path);
        $data = [
            'user_id' => $this->ci->session->user_id,
            'content' => ucfirst(xpost_txt('content'))
        ];
        return $data;
    }

    
    public function add() {
        $this->ci->check_loggedin();
        $data = $this->adit('add');
        $id = $this->ci->common_model->insert($this->table, $data);
        json_response($id);
    }


    public function edit() {
        $this->ci->check_loggedin();
        $data = $this->adit('edit');
        $id = xpost('id');
        //check if user post
        $user_id = $this->ci->post_model->get_details($this->type, $id, 'id', [], "user_id")->user_id;
        $this->ci->check_userdata($user_id);
        $this->ci->common_model->update($this->table, $data, ['id' => $id]);
        //get updated record and prepare for sending
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', ['all']);
        $data = $this->ci->post_model->prepare_post($row);
        json_response($data);
    }


    public function vote() {
        $this->ci->check_loggedin();
        $id = xpost('id');
        $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', [], "user_id, votes, voters");
        //ok, if poster, deny action
        if ($row->user_id == $this->ci->session->user_id)
            json_response("Cannot vote own {$this->type}!", false);
        //election time!
        $data = $this->ci->crud->vote($row->votes, $row->voters);
        $this->ci->common_model->update($this->table, $data, ['id' => $id]);
        //get updated record and prepare for sending
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', ['all']);
        $data = $this->ci->post_model->prepare_post($row);
        json_response($data);
    }


    public function delete() {
        $this->ci->check_loggedin();
        $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $id = xpost('id');
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', [], "user_id, can_delete, content");
        //check if user post
        $this->ci->check_userdata($row->user_id);
        //can i delete?
        if (!$row->can_delete)
            json_response('Action denied!', false);
        //delete post
        $content = $row->content;
        $deleted = $this->ci->common_model->delete($this->table, ['id' => $id]);
        if ($deleted) {
            if ($this->type == 'post') {
                //delete all comments
                $this->ci->common_model->delete(T_COMMENTS, ['post_id' => $id]);
            }
            //remove associated images
            $extracted = $this->ci->summernote->extract($content);
            unlink_files($this->img_upload_path, $extracted);
        }
        json_response('Successful!');
    }


    public function view_ajax($id) {
        $data['id'] = $id;
        $data['row'] = $this->ci->post_model->get_details($this->type, $id, 'id', [], "content");
        $this->ci->load->view($this->view_dir.'/view', $data);
    }


    public function edit_ajax($id) {
        $data['id'] = $id;
        $data['row'] = $this->ci->post_model->get_details($this->type, $id, 'id', [], "content");
        $this->ci->load->view($this->view_dir.'/edit', $data);
    }

}