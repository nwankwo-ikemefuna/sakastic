<?php
defined('BASEPATH') or die('Direct access not allowed');

class Web extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->page_scripts = ['posts'];
    }


    public function index() {
        $this->show_page_title = false;
        $this->show_disclaimer = false;
        $page_title = $this->site_name.' - '.$this->site_description;
        //if viewing user posts, does user exist?
        if (isset($_GET['user_posts'])) {
            $username = xget('user_posts');
            $count = $this->common_model->count_rows(T_USERS, ['username' => $username], -1);
            if ($count) {
                $data['user_posts'] = $username;
                $page_title = 'Posts by '.$username;
            } else {
                redirect(''); //back to home
            }
        } else {
            $data['user_posts'] = '';
        }
        $this->web_header($page_title);
        $this->load->view('posts/index', $data);
        $this->web_footer('home');
    }

}