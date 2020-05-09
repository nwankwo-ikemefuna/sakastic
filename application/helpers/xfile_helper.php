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
        //upload happens, everyone is happy
        $file_name = $ci->upload->data('file_name');
        //are we resizing?
        $resize = isset($conf['resize']) ? $conf['resize'] : false;
        if ($resize) {
            $width = $conf['resize_width'] ?? 500;
            $height = $conf['resize_height'] ?? 500;
            $thumb = resize_image($conf['path'], $file_name, $width, $height);
            //are we deleting original
            if (isset($conf['delete_origin']) && $conf['delete_origin']) {
                //delete original and return resized version
                unlink_file($conf['path'], $file_name, false);
                return ['status' => true, 'file_name' => $thumb];
            }
        }
        return ['status' => true, 'file_name' => $file_name];
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
            $file_name = $ci->upload->data('file_name');
            $upload_data['file_names'][] = $file_name;
            //are we resizing?
            $resize = isset($conf['resize']) ? $conf['resize'] : false;
            if ($resize) {
                $width = $conf['resize_width'] ?? 200;
                $height = $conf['resize_height'] ?? 200;
                resize_image($conf['path'], $file_name, $width, $height);
            }
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

function resize_image($path, $file_name, $width, $height) { 
    $ci =& get_instance(); 
    //config for image library
    $config['image_library'] = 'gd2';
    $config['source_image'] = $path.'/'.$file_name;
    $config['create_thumb'] = TRUE;
    $config['maintain_ratio'] = TRUE;
    $config['width'] = $width;
    $config['height'] = $height;

    //load image library
    $ci->load->library('image_lib', $config);
    
    if ( ! $ci->image_lib->resize()) {
        return $file_name; //if resize fails, return original image
    } else {
        $thumb = image_thumb($file_name);
        return $thumb;
    }
}

function unlink_file(string $path, $file = '', $delete_thumb = true) {
    //if file is not supplied, path is a complete file path
    $file_path = strlen($file) ? $path.'/'.$file : $path;
    if (is_file($file_path) && file_exists($file_path)) {
        //delete thumbnail (if any) and allowed
        if ($delete_thumb) {
            delete_thumbnail($file_path);
        }
        //delete file
        return unlink($file_path);
    }
    return false;
}

function unlink_files(string $path, array $files, $delete_thumb = true) {
    if (is_array($files) && !empty($files)) {
        foreach ($files as $file) {
            $file_path = $path.'/'.$file;
            if ( ! is_file($file_path) || ! file_exists($file_path)) continue;
            //delete thumbnail (if any) and allowed
            if ($delete_thumb) {
                delete_thumbnail($file_path);
            }
            //delete file
            unlink($file_path);
        }
    }
}

function delete_thumbnail($file_path) {
    $thumb = image_thumb($file_path);
    if (is_file($thumb) && file_exists($thumb)) {
        return unlink($thumb);
    }
    return false;
}

function image_thumb($file) { 
    $suffix = '_thumb.'; //eg cat.jpg becomes cat_thumb.jpg
    $thumb = str_lreplace('.', $suffix, $file);
    return $thumb;
}

function download_file($file_path, $file_name) { 
    force_download($file_path, NULL);
}