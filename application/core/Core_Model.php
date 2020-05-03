<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Core_Model extends CI_Model {

    // protected $table;

    public function __construct() {
        parent::__construct();
    }


    private function table_alias($table) {
        //table has alias? Let's get delim first (AS or space)
        $_delim = preg_match('/(^|\s)as($|\s|[^w])/i', $table) ? 'as' : ' ';
        $_table = explode($_delim, $table);
        //if alias is not set, use table name as alias
        $alias = isset($_table[1]) ? $_table[1] : $_table[0];
        return $alias;
    }


    private function joins($joins, $obj = 'db') {
        if ( ! is_array($joins) || empty($joins)) return;
        foreach ($joins as $j_table => $j_cond) {
            $j_on = $j_cond[0]; //join ON
            //check if type of join is set, else use left 
            $j_type = isset($j_cond[1]) && strlen($j_cond[1]) ? $j_cond[1] : 'left'; 
            //escape?
            $j_esc = isset($j_cond[2]) && strlen($j_cond[2]) ? $j_cond[2] : NULL; 
            $this->$obj->join($j_table, $j_on, $j_type, $j_esc);
        }
    }


    /**
     * get row details
     * @param  string       $table    [table name, could contain an alias]
     * @param  integer      $id       [description]
     * @param  string       $by       [description]
     * @param  integer      $trashed  [whether trashed or not, 0 no, 1 yes]
     * @param  array        $joins    [tables to be joined]
     * @param  string       $select   [fields to select]
     * @param  array        $where    [other where clauses]
     * @param  bool         $ajax     [whether to use db or datatables class]
     * @return void
     */
    private function prepare_query($table, $trashed = 0, $joins = [], $select = '', $where = [], $order = null, $group_by = '', $limit = '', $offset = 0, $ajax = false) {
        $obj = $ajax ? 'datatables' : 'db';
        $alias = $this->table_alias($table);
        //select is not set? Select all from main table
        if ( ! strlen($select)) {
            $select = $alias.'.*';
        } 
        $this->$obj->select($select);
        $this->$obj->from($table);
        $this->joins($joins, $obj);
        //general where
        //are we considering trashed?
        if ($trashed != -1) 
            $this->$obj->where($alias.'.trashed', $trashed);
        //other where
        if (is_array($where) && ! empty($where)) {
            foreach ($where as $field => $value) {
                //is $value an empty string or explicitly set as null? 
                if ($value === '' || $value === null) {
                    //escape is necessary incase where clause contains sql functions such as FIND_IN_SET(), etc
                    //eg: 'FIND_IN_SET(t.user_id, "3,1,2")' => ''
                    $this->$obj->where($field, NULL, false);
                } else {
                    $this->$obj->where($field, $value);
                }
            }  
        }
        if ($order != -1) {
            //order
            if (is_array($order) && ! empty($order)) {
                foreach ($order as $field => $direction) {
                    $this->db->order_by($field, $direction);
                }
            } elseif ($order == 'rand' || $order == 'rand()') {
                $this->db->order_by("RAND()");
            } else {
                $this->db->order_by($alias.'.date_created', 'desc');
            }
        }
        //group by
        if ($group_by !== '-') {
            $group_by = strlen($group_by) ? $group_by : $alias.'.id';
            $this->$obj->group_by($group_by);
        }
        if (strlen($limit)) 
            $this->$obj->limit($limit, $offset);
    }


    /**
     * get row details
     * @return object
     */
    public function get_row($table, $id = '', $by = '', $trashed = 0, $joins = [], $select = '', $where = [], $group_by = '', $return = 'object') {
        $alias = $this->table_alias($table);
        if (strlen($id)) {
            $by = strlen($by) ? $by : 'id';
            $where[$alias.'.'.$by] = $id;
        }
        $this->prepare_query($table, $trashed, $joins, $select, $where, -1, '-');
        $return = $return == 'object' ? 'row' : 'row_array';
        return $this->db->get()->$return();
    }


    /**
     * get rows
     * @return object
     */
    public function get_rows($table, $trashed = 0, $joins = [], $select = '', $where = [], $order = [], $group_by = '', $limit = '', $offset = 0, $return = 'object') {
        $this->prepare_query($table, $trashed, $joins, $select, $where, $order, $group_by, $limit, $offset);
        $return = $return == 'object' ? 'result' : 'result_array';
        return $this->db->get()->$return();
    }


    /**
     * get rows for ajax using Ignited Datatables library
     * @return object
     */
    public function get_rows_ajax($table, $keys, $buttons, $trashed = 0, $joins = [], $select = '', $where = [], $order = [], $group_by = '', $limit = '', $offset = 0) {
        $this->prepare_query($table, $trashed, $joins, $select, $where, $order, $group_by, $limit, $offset, true);
        //Bulk action column
        //Note: $1 assumes that the primary key column (usually id), is the first key
        $this->datatables->add_column('checker', xform_input('ba_record_idx[]', 'checkbox', '$1', false, ['class' => 'ba_record'], true), 'id');
        //actions column
        $this->datatables->add_column('actions', $buttons, join(',', $keys));
        return $this->datatables->generate();
    }


    /**
     * get aggregate value using functions such as MIN, MAX, SUM, AVG, etc
     * @return string
     */
    public function get_aggr_row($table, $type, $field, $where = [], $trashed = 0, $group_by = '') {
        $type = strtolower($type);
        $select = 'select_'.$type;
        $this->db->$select($field);
        $alias = $this->table_alias($table);
        if ( ! array_key_exists($alias.'trashed', $where)) {
            //are we considering trashed?
            if ($trashed != -1)
                $this->db->where($alias.'.trashed', $trashed);
        }
        $this->db->where($where);
        $this->db->group_by($group_by);
        return $this->db->get($table)->row()->$field;
    }


    /**
     * get record count
     * @return int
     */
    public function count_rows($table, $where = [], $trashed = 0) {
        // var_dump(func_get_args()); die;
        $alias = $this->table_alias($table);
        if ( ! array_key_exists($alias.'trashed', $where)) {
            //are we considering trashed?
            if ($trashed != -1)
                $this->db->where($alias.'.trashed', $trashed);
        }
        $this->db->where($where);
        return $this->db->count_all_results($table);
    }


    public function get_unique_row($table, $field, $is_edit = false, $edit_id = '', $where = []) {
        $param = xpost($field);
        if ( ! strlen($param)) return true;
        $where = array_merge([$field => $param], $where);
        //if edit, exclude the row being edited
        if ($is_edit) {
            //if edit id is not supplied, get from post
            $edit_id = strlen($edit_id) ? $edit_id : xpost('id');
            $where = array_merge(['id !=' => $edit_id], $where);
        } 
        $this->db->where($where);
        return $this->db->count_all_results($table) > 0;
    }


    public function insert(string $table, array $data) {
        if (!empty($data)) {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
        }
        return 0;
    }


    public function insert_batch(string $table, array $data) {
        if (!empty($data)) {
            return $this->db->insert_batch($table, $data);
        }
        return 0;
    }


    public function update(string $table, array $data, array $where) {
        if (!empty($data) && !empty($where)) {
            $this->db->where($where);
            return $this->db->update($table, $data);
        }
        return 0;
    }


    public function update_with_bulk($table, $data, $id_field = 'id') {
        $id = xpost($id_field);
        //is it bulk? let's check for comma in id field
        if (preg_match('/,/', $id)) {
            $record_idx = explode(',', $id);
            foreach ($record_idx as $rec_id) {
                $this->update($table, $data, ['id' => intval($rec_id)]);
            }
        } else {
            return $this->update($table, $data, ['id' => intval($id)]);
        }
        return 0;
    }


    public function update_batch(string $table, array $data, string $key = 'id') {
        if (!empty($data) && !empty($key)) {
            return $this->db->update_batch($table, $data, $key);
        }
        return 0;
    }


    public function delete($table, $where) { 
        if (!empty($where)) {
            $this->db->where($where);
            return $this->db->delete($table);
        }
        return 0;
    }


    public function delete_with_files($table, $where, $files) {
        //get the files to be deleted
        $row = $this->db->get_where($table, $where)->row();
        $paths = [];
        foreach ($files as $col => $_path) {
            $the_files = $row->$col; //file(s) name(s)
            //is the file column empty?
            if ( ! strlen($the_files)) continue;
            //get files as array
            $files_arr = explode(',', $the_files);
            if (empty($files_arr)) continue;
            $paths[$_path] = $files_arr;
        }
        $deleted = $this->delete($table, $where);
        if ($deleted) {
            //unlink files
            foreach ($paths as $path => $files_arr) {
                unlink_files($path, $files_arr);
            }
            return 1;
        }
        return 0;
    }


}