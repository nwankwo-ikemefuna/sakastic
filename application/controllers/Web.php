<?php
defined('BASEPATH') or die('Direct access not allowed');

class Web extends Core_controller {
    public function __construct() {
        parent::__construct();
        $this->page_scripts = ['web/js/posts'];
    }


    public function index() {
        $this->show_page_title = false;
        $this->show_disclaimer = false;
        $page_title = 'Home';
        //if viewing user posts, does user exist?
        if (isset($_GET['user_posts'])) {
            $username = xget('user_posts');
            $count = $this->common_model->count_rows(T_USERS, ['username' => $username]);
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


    public function profile($username) {
        $this->show_sidebar = false;
        $page_title = '';
        //if loggedin user, redirect to user dashboard
        if ($username == $this->session->user_username)
            redirect('dashboard'); 
        $row = $this->user_model->get_details($username, 'username', ['all']);
        if ($row) {
            $data['row'] = $row;
            $data['is_me'] = ($username == $this->session->user_username);
            $page_title = $username;
        } else {
            redirect(''); //back to home
        }
        $this->web_header($page_title);
        $this->load->view('user/index', $data); 
        $this->web_footer('profile');
    }

}