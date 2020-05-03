<?php
defined('BASEPATH') or die('Direct access not allowed');

/**
* Common controller to control objects, action and processes shared by other controllers.
* @author Nwankwo Ikemefuna.
* Date Created: 31/12/2019
* Date Modified: 31/12/2019
*/


class Common extends Core_controller {
    public function __construct() {
        parent::__construct();
    }


    public function trash_ajax() { 
        $this->crud->trash_ajax();
    }


    public function bulk_trash_ajax() { 
        $this->crud->bulk_trash_ajax();
    }


    public function trash_all_ajax() { 
        $this->crud->trash_all_ajax();
    }


    public function restore_ajax() { 
        $this->crud->restore_ajax();
    }


    public function restore_all_ajax() { 
        $this->crud->restore_all_ajax();
    }


    public function bulk_restore_ajax() { 
        $this->crud->bulk_restore_ajax();
    }


    public function delete_ajax() { 
        $this->crud->delete_ajax();
    }


    public function bulk_delete_ajax() {
        $this->crud->bulk_delete_ajax();
    }


    public function clear_trash_ajax() { 
        $this->crud->clear_trash_ajax();
    }


    public function upload_smt_image() { 
        $this->summernote->upload();
    }


    public function delete_smt_image() { 
        $this->summernote->delete();
    }
    
}