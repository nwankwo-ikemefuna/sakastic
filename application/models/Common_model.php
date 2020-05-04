<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Common_model extends Core_Model {
    public function __construct() {
        parent::__construct();
    }

    
    public function next_order($table) {
        //ensure order column exists
        if ( ! $this->db->field_exists('order', $table)) return; 
        $count = $this->count_rows($table);
        if ($count > 0) { 
            return $this->common_model->get_aggr_row($table, 'max', 'order') + 1;
        } 
        return 1;
    }

}