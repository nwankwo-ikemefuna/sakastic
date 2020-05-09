<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class User_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	public function sql($to_join = [], $select = "*", $where = []) {
		$arr = sql_select_arr($select);
		$select =  $select != '*' ? $arr['main'] : "u.*";
		$select .= join_select($arr, 'user_posts', "up.user_posts");
		$select .= join_select($arr, 'user_comments', "uc.user_comments");
		$select .= join_select($arr, 'user_total_content', "SUM(IFNULL(up.user_posts, 0) + IFNULL(uc.user_comments, 0))");
		$select .= join_select($arr, 'user_votes', "SUM(IFNULL(up.user_votes, 0) + IFNULL(uc.user_votes, 0))");
		$select .= join_select($arr, 'avatar', "'".AVATAR_GENERIC."'");
		$joins = [];
		//posts
		if (in_array('p', $to_join) || in_array('all', $to_join)) {
			$joins = array_merge($joins, 
				["(
					SELECT `user_id`, 
						COUNT(`user_id`) AS user_posts, 
						SUM(`votes`) AS user_votes
				    FROM `".T_POSTS."`
				    GROUP BY `user_id`
				) `up`" => ["`up`.`user_id` = `u`.`id`", 'left', false]]
			);
		}
		//comments
		if (in_array('c', $to_join) || in_array('all', $to_join)) {
			$joins = array_merge($joins, 
				["(
					SELECT `user_id`, 
						COUNT(`user_id`) AS user_comments, 
						SUM(`votes`) AS user_votes
				    FROM `".T_COMMENTS."`
				    GROUP BY `user_id`
				) `uc`" => ["`uc`.`user_id` = `u`.`id`", 'left', false]]
			);
		}
		return sql_data(T_USERS.' u', $joins, $select, $where);
	}


	public function get_details($id, $by = 'id', $to_join = [], $select = "*", $trashed = 0) {
		$sql = $this->sql($to_join, $select);
		return $this->get_row($sql['table'], $id, $by, $trashed, $sql['joins'], $sql['select'], [], $sql['group_by']);
	}


	public function get_all($to_join = [], $select = "", $where = [], $trashed = 0) {
		$sql = $this->sql($to_join, $select, $where);
		return $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $sql['where'], $sql['order'], $sql['group_by']);
	}


	private function data($usergroup) {
		$data = [
            'usergroup' => xpost('usergroup'),
            'first_name' => ucwords(xpost('first_name')),
            'last_name' => ucwords(xpost('last_name')),
            'email' => xpost('email'),
            'username' => xpost('username'),
            'phone' => xpost('phone'),
            'sex' => xpost('sex'),
            'password' => password_hash(xpost('password'), PASSWORD_DEFAULT)
        ];
		return $data;
	}


	public function add() { 
		$data = $this->data();
		$id = $this->insert(T_USERS, $data);
		return $id;
	}


	public function edit() { 
		$data = $this->data();
		$this->update(T_USERS, $data, ['id' => xpost('id')]);
	}

	
}