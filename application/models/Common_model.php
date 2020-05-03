<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Common_model extends Core_Model {
    public function __construct() {
        parent::__construct();
    }

    
    public function tags_sql() {
    	$select = "id, name, title";
        return sql_data(T_TAGS, [], $select, [], ['order' => 'asc']);
    }


    public function colors_sql() {
    	$select = "id, name, code, CONCAT('#', code) AS color_code";
        return sql_data(T_COLORS, [], $select, [], ['order' => 'asc']);
    }


    public function get_colors($icon = 'square') {
        $sql = $this->colors_sql();
        $colors = $this->common_model->get_rows($sql['table'], 0, $sql['joins'], $sql['select'], $sql['where']);
        $colors_arr = [];
        foreach ($colors as $row) {
            $colors_arr[$row->id] = '<i class="fa fa-'.$icon.'" style="color: #'.$row->code.'"></i> '.$row->name;
        }
        return $colors_arr;
    }


    public function currencies_sql() {
        $select = "id, name, code, CONCAT(country, ' ', name, ' (&#', code, ';)') AS curr_name";
        return sql_data(T_CURRENCIES, [], $select, [], ['order' => 'asc']);
    }


    public function states_sql($country_id = '') {
        $select = 's.id, s.name, s.order, COUNT(u.state) AS user_count';
        $joins = [T_COUNTRIES.' c' => ['c.id = s.country_id']];
        $joins = [T_USERS.' u' => ['u.state = s.id']];
        $where = strlen($country_id) ? ['s.country_id' => $country_id] : [];
        return sql_data(T_STATES.' s', $joins, $select, $where, ['s.order' => 'asc']);
    }


    public function get_states($country_id = '', $trashed = 0) {
        $sql = $this->states_sql($country_id);
        return $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $sql['where']);
    }


    public function status_sql($id = '') {
        $select = 's.id, s.type, s.name, s.title, s.key, s.color, s.bs_bg, s.icon';
        $where = strlen($id) ? ['s.id' => $id] : [];
        return sql_data(T_STATUS.' s', [], $select, $where);
    }


    public function get_status($id, $trashed = 0) {
        $sql = $this->status_sql($id);
        return $this->get_row($sql['table'], $id, 'id', $trashed, [], $sql['select'], $sql['where']);
    }


    public function get_statuses($type = '', $trashed = 0) {
        $sql = $this->status_sql();
        $where = strlen($type) ? array_merge($sql['where'], ['s.type' => $type]) : $sql['where'];
        return $this->get_rows($sql['table'], $trashed, [], $sql['select'], $where);
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