<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Post_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	public function sql($where = [], $select = "") {
		$select = strlen($select) ? $select : "p.*, u.username, COUNT(c.post_id) AS comment_count, 
			IF(FIND_IN_SET('{$this->session->user_id}', `p`.`voters`), 1, 0) AS voted,
			IF(p.user_id = '{$this->session->user_id}', 1, 0) AS is_user_post";
		$joins = [
			T_USERS.' u' => ['u.id = p.user_id'],
			T_COMMENTS.' c' => ['c.post_id = p.id']
		];
		return sql_data(T_POSTS.' p', $joins, $select, $where);
	}


	public function get_details($id, $select = "", $trashed = 0) {
		$sql = $this->sql([], $select);
		return $this->get_row($sql['table'], $id, 'id', $trashed, $sql['joins'], $sql['select']);
	}


	public function sort($sort_by) {
        switch ($sort_by) {
        	case 'popular':
                $order = ['COUNT(c.post_id)' => 'desc'];
                break;
            case 'voted':
                $order = ['p.votes' => 'desc'];
                break; 
            case 'oldest':
                $order = ['p.date_created' => 'asc'];
                break;
            case 'newest':
            default:
                $order = ['p.date_created' => 'desc'];
                break;
        }
        return $order;
    }


    public function search() {
        $search = xpost('search');
        $where = sprintf("(
            p.`content` LIKE '%s' OR
            u.`username` LIKE '%s')",
            "%{$search}%", "%{$search}%"
        );
        return $where;
    }


	public function get_posts($where = [], $select = "", $trashed = 0, $limit = '', $offset = 0) {
		$sql = $this->sql($where, $select);
		$order = strlen(xpost('sort_by')) ? $this->sort(xpost('sort_by')) : $sql['order'];
		return $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $where, $order, $sql['group_by'], $limit, $offset);
	}


	public function count_all($where = [], $trashed = 0) {
		return $this->count_rows(T_POSTS.' p', $where, $trashed);
	}


	private function data() {
		$data = [
			'user_id' => $this->session->user_id,
			'content' => ucfirst(xpost_txt('content'))
		];
		return $data;
	}


	public function add() { 
		$data = $this->data();
		$id = $this->insert(T_POSTS, $data);
		$row = $this->get_details($id);
		return $row;
	}


	public function edit() { 
		$id = xpost('id');
		$data = $this->data();
		$this->update(T_POSTS, $data, ['id' => xpost('id')]);
		$row = $this->get_details($id);
		return $row;
	}


	public function vote() { 
		$id = xpost('id');
		$type = xpost('type');
		//post or comment?
		if ($type == 'post') {
			$table = 'posts';
			$row = $this->get_details($id);
		} else {
			$table = 'comments';
			$row = $this->comment_model->get_details($id);
		}
		$voters = (array) split_us($row->voters);
		//already voted?
		if ( ! in_array($this->session->user_id, $voters)) {
			//nah, happy voting
			$voters[] = $this->session->user_id;
		} else {
			//voted, unvote
			unset($voters[$this->session->user_id]);
		}
		$data = ['voters' => join_us($voters)];
		$this->update($table, $data, ['id' => $id]);
		//return row
		return $row;
	}

	
}