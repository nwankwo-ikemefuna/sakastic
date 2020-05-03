<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class User_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	public function sql($where = [], $select = "") {
		$select = strlen($select) ? $select : "u.*";
		return sql_data(T_USERS.' u', [], $select, $where);
	}


	public function get_details($id, $select = "", $trashed = 0) {
		$sql = $this->sql(['id' => $id], $select);
		return $this->get_row($sql['table'], $id, 'id', $trashed, $sql['joins'], $sql['select'], [], $sql['group_by']);
	}


	public function get_all($select = "", $trashed = 0) {
		$sql = $this->sql($where, $select);
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