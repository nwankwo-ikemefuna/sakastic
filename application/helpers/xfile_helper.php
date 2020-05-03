<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ===== Documentation ===== 
Name: app_helper
Role: Helper
Description: custom general application helper
Author: Nwankwo Ikemefuna
Date Created: 31/12/2019
Date Modified: 31/12/2019
*/ 

function get_file($file, $default = null) {
    return is_file($file) && file_exists($file) ? $file : $default;
}

function create_dir($dir) {
    if ( ! file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

function upload_file($file_input, $conf) { 
    //make the dir if it doesn't exist
    create_dir($conf['path']);

    $ci =& get_instance(); 
    //config for file uploads
    $config['upload_path']          = $conf['path']; //path to save the file
    $config['allowed_types']        = $conf['ext'];  //extensions which are allowed
    $config['max_size']             = $conf['size']; //image size cannot exceed this in kilobytes
    $config['file_ext_tolower']     = TRUE; //force file extension to lower case
    $config['remove_spaces']        = TRUE; //replace space in file names with underscores to avoid break
    $config['detect_mime']          = TRUE; //detect type of file to avoid code injection                              
    $ci->load->library('upload', $config);
    if ( $_FILES[$file_input]['name'] == "" )  {//file is not selected
        $required = isset($conf['required']) ? $conf['required'] : true;
        $empty_msg = input_key_isset($conf, 'empty_msg', 'File not selected');
        return ['status' => ! $required, 'error' => $empty_msg, 'file_name' => null];
    }
    if ($_FILES[$file_input]['name'] != "" && ! $ci->upload->do_upload($file_input)) { 
        //upload does not happen when file is selected
        return ['status' => false, 'error' => $ci->upload->display_errors()];
    } else { 
        return ['status' => true, 'file_name' => $ci->upload->data('file_name')];
    }
}

function upload_files($file_input, $conf) { 
    create_dir($conf['path']);
    $ci =& get_instance(); 
    //nothing selected
    if ($_FILES[$file_input]['name'][0] == "") {
        //if file upload is required, throw error, else, carry go
        $required = isset($conf['required']) ? $conf['required'] : true;
        $empty_msg = input_key_isset($conf, 'empty_msg', 'File not selected');
        return ['status' => ! $required, 'error' => $empty_msg, 'file_name' => null];
    }
    // var_dump(count($_FILES[$file_input]['name'])); die;
        
    //check min files allowed
    $min = input_key_isset($conf, 'min', 1);
    if (count($_FILES[$file_input]['name']) < $min) {
        return ['status' => false, 'error' => $min > 0 ? 'You must upload at least '.$min.' file'.($min == 1 ? '' : 's') : 'You cannot upload any more files'];
    }
    //check max files allowed
    $max = input_key_isset($conf, 'max', 25);
    if (count($_FILES[$file_input]['name']) > $max) {
        return ['status' => false, 'error' => $max > 0 ? 'You cannot upload more than '.$max.' file'.($max == 1 ? '' : 's') : 'You cannot upload any more files'];
    }
    $upload_data['file_names'] = [];
    $upload_data['errors'] = [];
    $files_count = count($_FILES[$file_input]['name']);
    for($i = 0; $i < $files_count; $i++) {
        $_FILES['file']['name']     = $_FILES[$file_input]['name'][$i];
        $_FILES['file']['type']     = $_FILES[$file_input]['type'][$i];
        $_FILES['file']['tmp_name'] = $_FILES[$file_input]['tmp_name'][$i];
        $_FILES['file']['error']    = $_FILES[$file_input]['error'][$i];
        $_FILES['file']['size']     = $_FILES[$file_input]['size'][$i];     
        //config for file uploads
        $config['upload_path']          = $conf['path']; //path to save the file
        $config['allowed_types']        = $conf['ext'];  //extensions which are allowed
        $config['max_size']             = $conf['size']; //image size cannot exceed 64KB
        $config['file_ext_tolower']     = TRUE; //force file extension to lower case
        $config['remove_spaces']        = TRUE; //replace space in file names with underscores to avoid break
        $config['detect_mime']          = TRUE; //detect type of file to avoid code injection                              

        $ci->load->library('upload', $config);
        $ci->upload->initialize($config);
        
        // Upload file to server
        if ($ci->upload->do_upload('file')) {
            $upload_data['file_names'][] = $ci->upload->data('file_name');
        } else {
            $upload_data['errors'][] = $ci->upload->display_errors();
        }
    }
    
    if ( ! empty($upload_data['file_names'])) {
        return ['status' => true, 'file_name' => $upload_data['file_names']]; 
    } else {
        return ['status' => false, 'error' => $upload_data['errors']];
    }
}

function image_thumbnail($src, $title, $footer = '') { ?>
    <div class="card img_view_thumb">
        <img src="<?php echo $src; ?>" class="card-img-top clickable tm_image" title="<?php echo $title; ?>">
        <?php 
        if (strlen($footer)) { ?>
            <div class="footer"><?php echo $footer; ?></div>
            <?php
        } ?>
    </div>
    <?php
}

function download_file($file_path, $file_name) { 
    force_download($file_path, NULL);
}

function unlink_file(string $path, $file = '') {
    //if file is not supplied, path is a complete file path
    $file_path = strlen($file) ? $path.'/'.$file : $path;
    if (is_file($file_path) && file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

function unlink_files(string $path, array $files) {
    if (is_array($files) && !empty($files)) {
        foreach ($files as $file) {
            $file_path = $path.'/'.$file;
            if ( ! is_file($file_path) || ! file_exists($file_path)) continue;
            unlink($file_path);
        }
    }
}