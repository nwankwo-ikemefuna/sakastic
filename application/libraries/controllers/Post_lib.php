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
        $this->img_upload_path = $params['upload_path'] ?? '';
    }


    public function list($page, $where, $order = [], $count_joins = [], $having = '') {
        $per_page = 10;
        $page = paginate_offset($page, $per_page);
        $records = $this->ci->post_model->get_record_list($this->type, ['all'], '*', $where, $order, $per_page, $page, $having);
        $total_records = $this->ci->post_model->get_total_record($this->type, $where, $count_joins, $having);
        $data = paginate($records, $total_records, $per_page, "api/{$this->type}s/list");
        json_response($data);
    }


    public function view() {
        $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
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
        if ($crud_type == 'edit') { //edit
            $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        } else { //add
            if ($this->type == 'comment') {
                //require post and comment idx
                $this->ci->form_validation->set_rules('post_id', 'Post ID', 'trim|required');
                $this->ci->form_validation->set_rules('comment_id', 'Comment ID', 'trim|required');
            }
        }
        $this->ci->form_validation->set_rules('content', 'Post', 'trim|required');
        $this->ci->form_validation->set_rules('smt_images', 'Images', 'trim');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);

        if ($this->type == 'post') {
            $content = xpost('content');
            $raw_content = strip_tags($content);
            //TODO: prevent image only content
            $extracted = $this->ci->summernote->extract($content);
            if (!strlen($raw_content)) {
                json_response('You may have forgotten to type something.', false);
            }
            //delete images uploaded but were removed from the content field
            $this->ci->summernote->remove_files($content, xpost('smt_images'), $this->img_upload_path);
            $data = [
                'user_id' => $this->ci->session->user_id,
                'content' => ucfirst(xpost_txt('content'))
            ];
        } else {
            //strip image tags
            $content = ucfirst(xpost_txt('content'));
            $content = $this->ci->security->strip_image_tags($content);
            //if comment, append post and comment idx to array
            $data = [
                'user_id' => $this->ci->session->user_id,
                'content' => $content,
                'post_id' => xpost('post_id'),
                'parent_id' => xpost('comment_id')
            ];
        }
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


    public function follow() {
        $id = xpost('id');
        $this->ci->form_validation->set_rules('id', 'ID', 'trim|required');
        if ($this->ci->form_validation->run() === FALSE)
            json_response(validation_errors(), false);
        $followed_posts = $this->ci->session->tempdata('followed_posts') ?? [];
        //already following post?
        if ( ! array_key_exists(xpost('id'), $followed_posts)) {
            //not following, follow
            $followed_posts[$id] = date('Y-m-d H:i:s');
            $this->ci->session->set_tempdata('followed_posts', $followed_posts, FOLLOWED_POST_TTL);
        } else {
            //following, unfollow
            unset($followed_posts[$id]);
            $this->ci->session->set_tempdata('followed_posts', $followed_posts, FOLLOWED_POST_TTL);
        }
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
        $data['type'] = $this->type;
        $data['content'] = $this->ci->post_model->get_details($this->type, $id, 'id', [], "content")->content;
        $this->ci->load->view('posts/view_ajax', $data);
    }


    public function edit_ajax($id) {
        $select = 'content ' . ($this->type == 'post' ? '## comment_id' : ', parent_id');
        $row = $this->ci->post_model->get_details($this->type, $id, 'id', [], $select);
        $data['id'] = $id;
        $data['type'] = $this->type;
        $data['row'] = $row;
        $data['reply_type'] = $this->type == 'post' ? 'post' : ($row->parent_id == 0 ? 'comment' : 'reply');
        $this->ci->load->view('posts/edit_ajax', $data);
    }

}