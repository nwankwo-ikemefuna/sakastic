<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Post_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	public function sql($where = [], $select = "") {
		$select = strlen($select) ? $select : "p.*, u.username, 
			u.votes AS user_votes, COUNT(c.post_id) AS comment_count, 
			IF(FIND_IN_SET('{$this->session->user_id}', `p`.`voters`), 1, 0) AS voted,
			IF(p.user_id = '{$this->session->user_id}', 1, 0) AS is_user_post";
		$select .= ", '".base_url(AVATAR_GENERIC)."' AS avatar";
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
            p.`content` LIKE '%s')",
            "%{$search}%"
        );
        return $where;
    }


	public function get_posts($where = [], $select = "", $trashed = 0, $limit = '', $offset = 0) {
		$sql = $this->sql($where, $select);
		$order = strlen(xpost('sort_by')) ? $this->sort(xpost('sort_by')) : $sql['order'];
		$posts = $this->get_rows($sql['table'], $trashed, $sql['joins'], $sql['select'], $where, $order, $sql['group_by'], $limit, $offset);
		return $this->prepare_posts($posts);
	}


	private function prepare_posts($posts) {
        $data = [];
        $posts = is_array($posts) ? $posts : (array) $posts;
        foreach ($posts as $row) {
        	$data[] = $this->prepare_post($row);
        }
        return $data;
    }


    public function prepare_post($row) {
        $row = is_array($row) ? $row : (array) $row;
        //get first image in content if any to use as featured image
    	$extracted = $this->summernote->extract($row['content']);
    	$feat_image = $extracted ? base_url($this->img_upload_path.'/'.$extracted[0]) : '';
    	//remove all tags from content and truncate to some words
    	$raw_content = strip_tags($row['content']);
    	$max = 30;
    	if ($extracted || str_word_count($raw_content) > $max) {
    		$truncated = true;
    		$content = word_limiter($raw_content, $max);
    		//remove content since we need only snippet
    		unset($row['content']);
    	} else {
    		$truncated = false;
    		$content = $row['content'];
    	}
    	$data = array_merge($row, 
    		[
    			'truncated' => $truncated, 
    			'content' => $content, 
    			'feat_image' => $feat_image
    		]
    	);
        return $data;
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
		//return prepared post
		return $this->prepare_post($row);
	}


	private function type_data($type, $id) { 
		//post or comment?
		if ($type == 'post') {
			$table = 'posts';
			$row = $this->get_details($id, "p.user_id, p.votes, p.voters");
		} else {
			$table = 'comments';
			$row = $this->comment_model->get_details($id, "c.user_id, c.votes, c.voters");
		}
		$data = ['table' => $table, 'row' => $row];
		return $data;
	}


	public function vote() { 
		$type = xpost('type');
		$id = xpost('id');
		$t_data = $this->type_data($type, $id);
		$table = $t_data['table'];
		$row = $t_data['row'];
		$user_votes = $this->user_model->get_details($row->user_id, "u.votes")->votes;
		$voters = (array) split_us($row->voters);
		//already voted?
		if ( ! in_array($this->session->user_id, $voters)) {
			//nah, happy voting
			$voters[] = $this->session->user_id;
			$votes = $row->votes + 1;
			$user_votes += 1;
		} else {
			//voted, unvote
			$key = array_search($this->session->user_id, $voters);
			unset($voters[$key]);
			$voters = array_values($voters);
			$votes = $row->votes - 1;
			$user_votes -= 1;
		}
		//update posts
		$data = [
			'votes' => $votes,
			'voters' => join_us($voters)
		];
		$this->update($table, $data, ['id' => $id]);
		//update user votes
		$this->update(T_USERS, ['votes' => $user_votes], ['id' => $row->user_id]);
		//get updated record and return prepared post
		$row = $this->get_details($id);
		return $this->prepare_post($row);
	}

	
}