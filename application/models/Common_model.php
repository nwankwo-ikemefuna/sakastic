<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Common_model extends Core_Model {
    public function __construct() {
        parent::__construct();
    }


    public function country_sql($select = '*', $where = []) {
        $select = strlen($select) ? $select : 'c.id, c.name, c.nationality, c.order';
        return sql_data(T_COUNTRIES.' c', [], $select, $where);
    }


    public function get_countries($select = '*', $where = [], $trashed = 0) {
        $sql = $this->country_sql($select, $where);
        return $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $sql['where']);
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