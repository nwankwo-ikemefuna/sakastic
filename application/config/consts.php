<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//get instance of CodeIgniter's super object
$CI =& get_instance();

//Tables
$tables = $CI->db->list_tables();
foreach ($tables as $table) {
	$const_table = 'T_' . strtoupper($table);	
	define($const_table, $table);
} 

//users
define('ADMIN', 1);
define('USER', 2);
define('ALL_USERS', [ADMIN, USER]);

// User Rights 
define('VIEW', 1);
define('ADD', 2);
define('EDIT', 3);
define('DEL', 4);

define('SITE_NAME', 'Sakastic');
define('SITE_DESCRIPTION', 'Just for fun');
define('SITE_LOGO', 'assets/common/img/logo/logo.png');
define('SITE_FAVICON', 'assets/common/img/logo/favicon.png');
define('IMAGE_404', 'assets/common/img/icons/not_found.png');
//avatar
define('AVATAR_GENERIC', 'assets/common/img/avatar/generic.png');
define('AVATAR_MALE', 'assets/common/img/avatar/male.png');
define('AVATAR_FEMALE', 'assets/common/img/avatar/female.png');

//misc
define('SEX_MALE', 1);
define('SEX_FEMALE', 2);
define('C_NIGERIA', 135);

define('SITE_INFO_MAIL', 'info@softbytech.com');
define('SITE_NOTIF_MAIL', 'notify@softbytech.com');