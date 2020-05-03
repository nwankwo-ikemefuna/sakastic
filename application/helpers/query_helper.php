<?php 
function sql_data($table, $joins, $select, $where, $order = [], $group_by = '') {
	return ['table' => $table, 'joins' => $joins, 'select' => $select, 'where' => $where, 'order' => $order, 'group_by' => $group_by];
}

function full_name_select($table_alias = '', $with_alias = true, $alias = 'full_name', $prefix = '', $affix = '') {
	$tbl_alias = strlen($table_alias) ? "{$table_alias}." : '';
	//individual names
	$pfx = strlen($table_alias) ? "{$table_alias}_" : '';
	$select = "
		IFNULL({$tbl_alias}{$prefix}{$affix}title, '') AS {$pfx}title,  
		IFNULL({$tbl_alias}{$prefix}{$affix}first_name, '') AS {$pfx}first_name, 
		IFNULL({$tbl_alias}{$prefix}{$affix}last_name, '') AS {$pfx}last_name,  
		IFNULL({$tbl_alias}{$prefix}{$affix}other_name, '') AS {$pfx}other_name";
	//concatenated name
	$select .= ", TRIM(
		CONCAT(
			IFNULL({$tbl_alias}{$prefix}title{$affix}, ''), ' ', 
			IFNULL({$tbl_alias}{$prefix}first_name{$affix}, ''), ' ', 
			IFNULL({$tbl_alias}{$prefix}last_name{$affix}, ''), ' ', 
			IFNULL({$tbl_alias}{$prefix}other_name{$affix}, '')
		))";
	$select .= $with_alias ? " AS {$alias}" : '';
	return $select;
}

function user_age_select($tbl_alias = '', $alias = 'age') {
	$tbl_alias = strlen($tbl_alias) ? "{$tbl_alias}." : '';
	$select = "IFNULL( 
	CONCAT(TIMESTAMPDIFF(YEAR, {$tbl_alias}date_of_birth, CURDATE()), (IF(TIMESTAMPDIFF(YEAR, {$tbl_alias}date_of_birth, CURDATE()) = 1, ' year', ' years'))), '') AS {$alias}";
	return $select;
}

function db_user_title($title) {
	return strlen(trim($title)) ? ucwords($title) : NULL;
}

function datetime_select($field, $alias = '', $full_month = false) {
	$month = $full_month ? 'M' : 'b';
	$as_alias = strlen($alias) ? "AS {$alias}" : '';
	return "DATE_FORMAT({$field}, '%D %{$month}, %Y at %h:%i %p') {$as_alias}";
}

function price_select($code_col, $price_col, $alias = 'amount', $precision = 0) {
	return "CONCAT('&#', {$code_col}, ';', CONVERT(FORMAT({$price_col}, {$precision}) using utf8)) AS {$alias}";
}

function file_select($path, $file_col, $alias = 'file', $default = null) {
	return "CONCAT('{$path}', '/', {$file_col}) AS {$alias}";
}

function find_in_set_mult($params, $field) {
	if (empty($params)) return [];
	$params_arr = is_array($params) ? $params : split_us($params);
	$where = [];
    foreach ($params_arr as $param) {
        $where[] = "FIND_IN_SET({$param}, {$field}) > 0";
    }
    $where = join(" OR ", $where);
    $where = '('.$where.')';
    return $where;
}