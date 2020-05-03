<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Comment_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	/* =========== Posts =============== */
	public function sql($where = [], $select = "") {
		$select = strlen($select) ? $select : "p.*, u.username";
		$joins = [
			T_USERS.' u' => ['u.id = p.user_id']
		];
		return sql_data(T_POSTS.' p', $joins, $select, $where);
	}


	public function get_details($id, $select = "", $trashed = 0) {
		$sql = $this->sql(['id' => $id], $select);
		return $this->get_row($sql['table'], $id, 'id', $trashed, $sql['joins'], $sql['select'], [], $sql['group_by']);
	}


	private function sort($sort_by) {
        switch ($sort_by) {
            case 'newest':
                $order = ['p.date_created' => 'desc'];
                break;
            case 'rated':
                $order = ['p.rating' => 'desc'];
                break;         
            case 'popular':
            default:
                $order = ['FIND_IN_SET('.TAG_FEATURED.', p.tags) DESC' => ''];
                break;
        }
        return $order;
    }


    private function search() {
        $search = xpost('search');
        $where = sprintf("(
            p.`name` LIKE '%s' OR
            p.`barcode` LIKE '%s' OR
            p.`serial_no` LIKE '%s' OR
            p.`description` LIKE '%s')",
            "%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"
        );
        return $where;
    }


    private function filter($where, $sql) {
        //search term
        if (strlen(xpost('search'))) {
            $this_where = $this->search();
            $where = array_merge($where, [$this_where => null]);
        } 
        //price
        if (strlen(xpost('price_min')) && strlen(xpost('price_max'))) {
            $price_min = xpost('price_min');
            $price_max = xpost('price_max');
            $this_where = ['p.price >=' => $price_min, 'p.price <=' => $price_max];
            $where = array_merge($where, $this_where);
        } 
        //categories
        if ( ! empty(xpost('cat_idx'))) {
            $cat_idx = join_us(xpost('cat_idx'));
            $this_where = "FIND_IN_SET(p.cat_id, '{$cat_idx}') > 0";
            $where = array_merge($where, [$this_where => null]);
        } 
        //sizes
        if ( ! empty(xpost('sizes'))) {
            $sizes = join_us(xpost('sizes'));
            $this_where = "FIND_IN_SET(p.size, '{$sizes}') > 0";
            $where = array_merge($where, [$this_where => null]);
        } 
        //min rating
        if ( ! empty(xpost('min_rating'))) {
            $min_rating = xpost('min_rating');
            $this_where = ['p.rating >=' => $min_rating];
            $where = array_merge($where, $this_where);
        } 
        //colors
        if ( ! empty(xpost('colors'))) {
            $colors = xpost('colors');
            $this_where = find_in_set_mult($colors, 'p.colors');
            $where = array_merge($where, [$this_where => null]);
        } 
        //sorting
        $order = strlen(xpost('sort_by')) ? $this->sort(xpost('sort_by')) : $sql['order'];
        $data = ['where' => $where, 'order' => $order];
        return $data;
    }


	public function get_posts($where = [], $select = "", $trashed = 0, $limit = '', $offset = 0) {
		$sql = $this->sql($where, $select);
		return $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $sql['where'], $sql['order'], $sql['group_by'], $limit, $offset);
	}


	public function count_posts($where = [], $trashed = 0) {
		return $this->count_rows(T_POSTS, $where, $trashed);
	}


	/* =========== Comments =============== */
	private function data($post_id = 0, $level = 0) {
		$data = [
			'user_id' => $this->session->user_id,
			'post_id' => $post_id,
			'level' => $level,
			'content' => ucfirst(xpost_txt('content'))
		];
		return $data;
	}


	public function add() { 
		$data = $this->data(xpost('post_id'), xpost('level'));
		$id = $this->insert(T_POSTS, $data);
		return $id;
	}


	public function edit() { 
		$data = $this->data(xpost('post_id'), xpost('level'));
		$this->update(T_POSTS, $data, ['id' => xpost('id')]);
	}
	
}