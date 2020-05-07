<?php 
function xdump($var = null) {
    if (isset($var)) {
        var_dump($var);
        exit;
    }
}

function last_sql() {
	$ci =& get_instance();
    echo $ci->db->last_query();
}

function total_queries() {
	$ci =& get_instance();
    echo $ci->db->total_queries();
}

function pretty_print($var) {
  echo "<pre>", var_dump($var), "</pre>";
}