<?php 
function site_meta($page_title = '') { 
    $ci =& get_instance();
    ?>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <title><?php echo $page_title; ?> | <?php echo $ci->site_name; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="description" content="<?php echo $ci->site_description; ?>" />
    <meta name="author" content="<?php echo $ci->site_author; ?>"  />
    <meta name="keywords" content="">

    <link rel="shortcut icon" type="image/png" href="<?php echo base_url(SITE_FAVICON); ?>" />
    <?php
}

function load_scripts(array $scripts, $path) {
    if ($scripts) {
        foreach ($scripts as $script) { 
            $script_url = base_url().$path.'/'.$script.'.js'; ?>
            <script src="<?php echo $script_url; ?>"></script>
            <?php echo "\r\n";
        }
    }
}

function xpost($field, $default = NULL, $xss_clean = TRUE) {
	$ci =& get_instance();
    $data = $ci->input->post($field, $xss_clean);
    $data = ($data == 0 || !empty($data)) ? $data : $default;
	return $data;
}

function xpost_txt($field, $default = NULL, $xss_clean = TRUE) {
	$ci =& get_instance();
    $data = $ci->input->post($field, $xss_clean);
    $data = ($data == 0 || !empty($data)) ? nl2br_except_pre(ucfirst($data)) : $default;
	return $data;
}

function xget($field, $xss_clean = TRUE) {
	$ci =& get_instance();
	return $ci->input->get($field, $xss_clean);
}

function query_param($key, $val) {
	return (empty($_GET) ? '?' : '&').$key.'='.$val;
}

function trashed_record_list() {
	return (int) (isset($_GET['trashed']) && $_GET['trashed'] == 1);
}

function get_requested_page() {
	return current_url() . (strlen($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
}

function get_requested_resource() {
    //we don't want the scheme and host pls
    $requested_page = get_requested_page();
    $resource = str_replace(base_url(), '', $requested_page);
    return $resource;
}

function get_requested_resource_ajax() {
    $ci =& get_instance();
    $requested_page = isset($_SESSION['ajax_requested_page']) 
        ? str_replace(base_url(), '', $ci->session->ajax_requested_page)
        : 'user';
    //is there a leading slash?
    $requested_page = $requested_page[0] === '/' ? substr($requested_page, 1) : $requested_page; 
    //ensure we don't return asset files like images, js, css, etc
    //our urls do not contain the .php extension, so we can consider anything that has a dot to be a nuisance.
    if (preg_match('/\./', $requested_page)) {
        //set to default
        $requested_page = 'user';
    }
    //finally, does it exist?
    if ( ! file_get_contents(base_url($requested_page))) {
        //set to default
        $requested_page = 'user';
    }
    return $requested_page;
}

function response_headers(
	$content_type = 'application/json', 
	$allow_origin = '*', 
	$allow_cred = 'true',
	$allow_headers = 'X-Requested-With, Content-Type, Origin, Method, X-API-KEY, Cache-Control, Pragma, Accept, Accept-Encoding',
	$cache_control = 'no-cache, must-revalidate') {
	header("Access-Control-Allow-Origin: " . 		$allow_origin);
	header("Access-Control-Allow-Credentials: " . 	$allow_cred);
	header("Access-Control-Allow-Headers: " . 		$allow_headers);
	header("Content-Type: " . 						$content_type);
	header("Cache-Control: " . 						$cache_control);
}

function json_response($data = null, $status = true, $code = HTTP_OK) {
    http_response_code($code);
    $res = ['status' => $status];
    $body = $status ? ['body' => ['msg' => $data]] : ['error' => $data];
    $res = array_merge($res, $body);
    echo json_encode($res);
    exit;
}

function json_response_db($is_update = false) {
	$ci =& get_instance();
	$error = $is_update ? 'No changes detected' : 'Sorry, something went wrong. If issue persists, report to site administrator';
	return $ci->db->affected_rows() > 0 ? json_response() : json_response($error, false);
}

function password_strength($password) {
    //ensure password is specified first
    if (!strlen($password)) return ['has_err' => true, 'err' => 'Password is required'];

    $uppercase = preg_match('/[A-Z]/', $password); //at least 1 uppercase letter
    $lowercase = preg_match('/[a-z]/', $password); //at least 1 lowercase letter
    $number    = preg_match('/[0-9]/', $password); //at least 1 number 
    $character = preg_match('/(?=\S*[\W])/', $password); //at least 1 special xter
    $has_err = false;
    $err = "Password is missing the following: ";
    if( ! $uppercase) {
        $err .= ($has_err ? ', ':'') . 'at least 1 uppercase letter';
        $has_err = true;
    }
    if( ! $lowercase) {
        $err .= ($has_err ? ', ':'') . 'at least 1 lowercase letter';
        $has_err = true;
    }
    if( ! $number) {
        $err .= ($has_err ? ', ':'') . 'at least 1 digit';
        $has_err = true;
    }
    if( ! $character) {
        $err .= ($has_err ? ', ':'') . 'at least 1 special character';
        $has_err = true;
    }
    return ['has_err' => $has_err, 'err' => $err];
}

function verify_url_title($url_title, $real_title, $redirect = '') {
	if ($url_title != url_title($real_title)) 
        redirect($redirect);
}

function scandir_recursive($dir) {
    $result = [];
    foreach(scandir($dir) as $filename) {
        //remove annoying dots
        if (in_array($filename[0], ['.', '..'])) continue;
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        if (is_dir($path)) {
        	//if dir, run through
            foreach (scandir_recursive($path) as $childFilename) {
                $result[] = $filename . DIRECTORY_SEPARATOR . $childFilename;
            }
        } else {
            $result[] = $filename;
        }
    }
    return $result;
}
