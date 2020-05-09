<?php
defined('BASEPATH') or exit('Direct access to script not allowed');

class Post_model extends Core_Model {
	public function __construct() {
		parent::__construct();
	}


	public function sql($type, $to_join = [], $select = "*", $where = []) {
		$alias = $type[0]; //p OR c
		$arr = sql_select_arr($select);
		$select =  $select != '*' ? $arr['main'] : "{$alias}.*";
		if ($type == 'post') {
			$select .= join_select($arr, 'post_id', "p.id");
			$select .= join_select($arr, 'comment_id', "0");
			$select .= join_select($arr, 'comment_count', "COUNT(c.post_id)");
		} else {
			$select .= join_select($arr, 'comment_id', "c.id");
			$select .= join_select($arr, 'comment_count', "COUNT(r.parent_id)");
			$select .= join_select($arr, 'followed', "0");
		}
		$select .= join_select($arr, 'username', "u.username");
		$select .= join_select($arr, 'user_votes', "SUM(IFNULL(up.user_votes, 0) + IFNULL(uc.user_votes, 0))");
		$select .= join_select($arr, 'user_posts', "IFNULL(up.user_posts, 0)");
		$select .= join_select($arr, 'user_comments', "IFNULL(uc.user_comments, 0)");
		$select .= join_select($arr, 'voted', "IF(FIND_IN_SET('{$this->session->user_id}', `{$alias}`.`voters`), 1, 0)");
		$select .= join_select($arr, 'is_user_post', "IF({$alias}.user_id = '{$this->session->user_id}', 1, 0)");
		$select .= join_select($arr, 'avatar', file_select('uploads/images/users', 'u.photo', avatar_select_default('u.sex')));
		$joins = [];
		if ($type == 'post') {
			//comments
			if (in_array('c', $to_join) || in_array('all', $to_join)) {
				$joins = array_merge($joins, 
					[T_COMMENTS.' c' => ['c.post_id = p.id']]
				);
			}
		} else {
			//replies
			if (in_array('r', $to_join) || in_array('all', $to_join)) {
				$joins = array_merge($joins, 
					[T_COMMENTS.' r' => ['r.parent_id = c.id']]
				);
			}
		}
		//users
		if (in_array('u', $to_join) || in_array('all', $to_join)) {
			$joins = array_merge($joins, 
				[T_USERS.' u' => ["u.id = {$alias}.user_id"]]
			);
		}
		//user posts and votes
		if (in_array('up', $to_join) || in_array('all', $to_join)) {
			$joins = array_merge($joins, 
				["(
					SELECT `p`.`user_id`, 
						COUNT(`p`.`user_id`) AS user_posts, 
						SUM(`p`.`votes`) AS user_votes
				    FROM `".T_USERS."` `u`
				    LEFT JOIN `".T_POSTS."` `p` ON 
				    	`p`.`user_id` = `u`.`id`
				    GROUP BY `p`.`user_id`
				) `up`" => ["`up`.`user_id` = `u`.`id`", 'left', false]]
			);
		}
		//user comments and votes
		if (in_array('uc', $to_join) || in_array('all', $to_join)) {
			$joins = array_merge($joins, 
				["(
					SELECT `c`.`user_id`, 
						COUNT(`c`.`user_id`) AS user_comments, 
						SUM(`c`.`votes`) AS user_votes
				    FROM `".T_USERS."` `u`
				    LEFT JOIN `".T_COMMENTS."` `c` ON 
				    	`c`.`user_id` = `u`.`id`
				    GROUP BY `c`.`user_id`
				) `uc`" => ["`uc`.`user_id` = `u`.`id`", 'left', false]]
			);
		}
		$table = $type.'s '.$alias; //posts p OR comments c
		//default order: posts desc, comments asc
		$order = ['date_created' => ($type == 'post') ? 'desc' : 'asc'];
		return sql_data($table, $joins, $select, $where, $order);
	}


	public function sort($sort_by, $type) {
		$alias = $type[0]; //p OR c
        switch ($sort_by) {
        	case 'popular': //with most comments or replies
        		$col = $type == 'post' ? 'c.post_id' : 'r.parent_id';
                $order = ["COUNT({$col})" => 'desc'];
                break;
            case 'voted': //most upvoted
                $order = ["{$alias}.votes" => 'desc'];
                break; 
            case 'oldest': //oldest first
                $order = ["{$alias}.date_created" => 'asc'];
                break;
            case 'newest': //newest first
            default:
                $order = ["{$alias}.date_created" => 'desc'];
                break;
        }
        return $order;
    }


    public function search() {
        $search = xpost('search');
        //properly escape string
        $search = $this->db->escape_str($search, true);
        $where = sprintf("(
            p.`content` LIKE '%s')",
            "%{$search}%"
        );
        return $where;
    }


    public function trending_query() {
        //this week. skip if no comment
        $data['where'] = ['YEARWEEK(p.date_created) = YEARWEEK(NOW())' => null];
		$data['order'] = ['COUNT(c.post_id)' => 'desc'];
		$data['having'] = 'COUNT(c.post_id) > 0';
		$data['count_joins'] = ["(
			SELECT post_id 
		    FROM `".T_COMMENTS."`
		    GROUP BY post_id
   	 		HAVING COUNT(post_id) > 0
		) `c`" => ["`c`.`post_id` = `p`.`id`", 'left', false]];
		return $data;
    }


    public function followed_query() {
        $followed_posts = $this->session->tempdata('followed_posts') ?? [];
		$post_idx = join_us(array_keys($followed_posts));
		$data['where'] = ["FIND_IN_SET(p.id, '{$post_idx}')" => null];
		return $data;
    }


    public function filter() {
        $data = ['where' => [], 'order' => [], 'count_joins' => [], 'having' => ''];
        //user posts?
        $username = xpost('user');
        if (strlen($username)) {
            //does the user even exists?
            $row = $this->user_model->get_details($username, 'username', [], "id");
            if ($row) {
                $data['where'] = ['p.user_id' => $row->id];
            }
        }
        //type?
        if (strlen(xpost('type'))) {
            switch (xpost('type')) {

            	//recent
            	case 'recent':
            		$data['order'] = ['p.date_created' => 'desc'];
            		break;

            	//trending
            	case 'trending':
            		$query = $this->trending_query();
            		$data['where'] = array_merge($query['where'], ['c.post_id = p.id' => null]);
            		$data['order'] = $query['order'];
            		$data['count_joins'] = $query['count_joins'];
            		$data['having'] = $query['having'];
            		break;

            	//followed
            	case 'followed':
            		$query = $this->followed_query();
            		$data['where'] = $query['where'];
            		break;
            }
        } 
        //searching?
        if (strlen(xpost('search'))) {
            $this_where = $this->search();
            //merge to previous where
            $where = array_merge($data['where'], [$this_where => null]);
        } 
        return $data;
    }


	public function get_details($type, $id, $by = 'id', $to_join = [], $select = "*", $trashed = 0) {
		$sql = $this->sql($type, $to_join, $select);
		return $this->get_row($sql['table'], $id, $by, $trashed, $sql['joins'], $sql['select'], [], $sql['group_by']);
	}


	public function get_record_list($type, $to_join, $select = "*", $where = [], $order = [], $limit = '', $offset = 0, $having = '', $simple = false) {
		$sql = $this->sql($type, $to_join, $select, $where);
		//is sort type set? else use default
		$order = strlen(xpost('sort_by')) ? $this->sort(xpost('sort_by'), $type) : (!empty($order) ? $order : $sql['order']);
		$posts = $this->get_rows($sql['table'], 0, $sql['joins'], $sql['select'], $where, $order, $sql['group_by'], $limit, $offset, $having);
		return $this->prepare_posts($posts, $simple);
	}


	public function get_total_record($type, $where = [], $joins = [], $having = '') {
		$table = $type.'s '.$type[0]; //posts p OR comments c 
		return $this->count_rows($table, $where, 0, $joins, $having);
	}


	private function prepare_posts($posts, $simple) {
		$data = [];
        $posts = is_array($posts) ? $posts : (array) $posts;
        foreach ($posts as $row) {
        	$data[] = $this->prepare_post($row, $simple);
        }
        return $data;
    }


    public function prepare_post($row, $simple = false) {
        $row = is_array($row) ? $row : (array) $row;
        //get thumbnail of first image in content if any to use as featured image
    	$extracted = $this->summernote->extract($row['content']);
    	$feat_image = $extracted ? image_thumb($this->img_upload_path.'/'.$extracted[0]) : '';
    	//remove all tags from content and truncate to some words
    	$raw_content = strip_tags($row['content']);
    	$max = $simple ? 7 : 30;
    	//are we viewing single post?
    	$is_post_view = (xpost('is_post_view') == 1);
    	//simple (eg for sidebar) ?
	    if ($simple) {
	    	$truncated = true;
	    	$content = word_limiter($raw_content, $max);
	    } else {
	    	if ($is_post_view) {
	    		$truncated = false;
		    	$content = $row['content'];
		    } else {
		    	//if post has at least 1 image or words > $max, we truncate
		    	if ($extracted || str_word_count($raw_content) > $max) {
		    		$truncated = true;
		    		$content = word_limiter($raw_content, $max);
		    		//remove content since we need only snippet
		    		unset($row['content']);
		    	} else {
		    		$truncated = false;
		    		$content = $row['content'];
		    	}
		    }
		}
    	//remove things we don't need
    	unset($row['voters']);
    	//following post?
    	$followed_posts = $this->session->tempdata('followed_posts') ?? [];
    	$followed = array_key_exists($row['id'], $followed_posts);
    	$data = array_merge($row, 
    		[
    			'votes' => shorten_number($row['votes']), 
    			'user_votes' => isset($row['user_votes']) ? shorten_number($row['user_votes']) : '', 
    			'user_posts' => isset($row['user_posts']) ? shorten_number($row['user_posts']) : '', 
    			'user_comments' => isset($row['user_comments']) ? shorten_number($row['user_comments']) : '', 
    			'comment_count' => isset($row['comment_count']) ? shorten_number($row['comment_count']) : 0, 
    			'truncated' => $truncated, 
    			'content' => $content, 
    			'feat_image' => base_url($feat_image),
    			'followed' => $followed
    		]
    	);
        return $data;
    }

	
}