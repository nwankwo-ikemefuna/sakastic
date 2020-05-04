<?php
defined('BASEPATH') or die('Direct access not allowed');

class Errors extends Core_controller {
    public function __construct() {
        parent::__construct();
    }


    public function error_404() { 
        if ($this->input->is_ajax_request()) {
            $this->load->view('errors/html/error_404_ajax');
        } else {
            $this->web_header('404::Page Not Found');
            $this->load->view('errors/html/error_404');
            $this->web_footer();
        }
    }


    public function forbidden() { 
        if ($this->input->is_ajax_request()) {
            $this->load->view('errors/html/error_403_ajax');
        } else {
            $this->web_header('403::Forbidden');
            $this->load->view('errors/html/error_403');
            $this->web_footer();
        }
    }

}