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

function is_assoc_array(array $arr) {
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function closest_val_lower($array, $number) {
    $lesser = [];
    foreach ($array as $val) {
        if ($val < $number) {
            $lesser[] = $val;
        }
    }
    //if no value is greater than number, return the number
    return empty($lesser) ? $number : max($lesser);
}

function closest_val_upper($array, $number) {
    $greater = [];
    foreach ($array as $val) {
        if ($val > $number) {
            $greater[] = $val;
        }
    }
    //if no value is greater than number, return the number
    return empty($greater) ? $number : min($greater);
}

function pluralize_word($word, $count) {
    //pluralize word if count 0 or > 1
    if ($count == 1) return singular($word);
    return plural($word);
}

function hash_value($value, $algorithm = 'sha512') {
    return hash($algorithm, $value);
}

function generate_api_key($user_id) { 
    //format: md5($user_id)-md5(random string)
    $api_key = substr(md5($user_id), 0, 15);
    $api_key .= '-';
    $api_key .= substr(md5(microtime().rand()), 0, 24);
    return shuffle_string_case($api_key);
}

function shuffle_string_case($str) {
    $len = strlen($str); 
    for ($i = 0; $i < $len; $i++) {
        $str[$i] = (rand(0, 100) > 50
            ? strtoupper($str[$i])
            : strtolower($str[$i]));
    }
    return $str;
}

function time_ago($time, $units = 1) { //return time in ago
    //add mysql-server time difference to time;
    $time_diff = 0;
    $time = strtotime("+$time_diff hours", strtotime($time));
    $now = time(); //current time
    return strtolower(timespan($time, $now, $units)). ' ago';
}

function x_date($date, $with_ago = false) { //format date in the form eg 23rd Aug, 2018 from timestamp in db
    if ($date == NULL) return NULL;
    $x_date = date("jS M, Y", strtotime($date));
    if ( ! $with_ago) {
        $x_date = $x_date;
    } else {
        $x_date = $x_date . ' (' . time_ago($date) . ')';
    }
    return $x_date;
}

function x_date_full($date, $with_ago = false) { //format date in the form eg 23rd August, 2018 from timestamp in db
    if ($date == NULL) return NULL;
    $x_date = date("jS F, Y", strtotime($date));
    if ( ! $with_ago) {
        $x_date = $x_date;
    } else {
        $x_date = $x_date . ' (' . time_ago($date) . ')';
    }
    return $x_date;
}

function x_date_time($date, $with_ago = false) {
    if ($date == NULL) return NULL;
    $x_date = date("jS F, Y", strtotime($date)). ' at ' .date("h:i A", strtotime($date));
    if ( ! $with_ago) {
        $x_date = $x_date;
    } else {
        $x_date = $x_date . ' (' . time_ago($date) . ')';
    }
    return $x_date;
}

function x_time_12hour($date) { //eg 05:20 PM
    if ($date == NULL) return NULL;
    return date("h:i A", strtotime($date));
}

function x_time_24hour($date) { //eg 17:20
    if ($date == NULL) return NULL;
    return date("H:i A", strtotime($date));
}

function date_today_db() { //today date for insertion into db
    return $today = date('Y-m-d H:i:s'); //in the format yyyy-mm-dd
}

function date_today_dmy() { 
    return $today = date('d/m/Y'); //in the format dd/mm/yyyy
}

function sluggify_string($string, $separator = '-') { //get slug from titles and captions for use in URL
    return url_title($string, $separator, $lowercase = TRUE);
}

function get_currency_symbol($currency_code) {
    return '&#'.$currency_code.';';
}

function join_us($data, $delim = ',') {
    if (empty($data)) return null; 
    return is_array($data) ? join($delim, $data) : $data;
}

function split_us($data, $delim = ',') {
    if (empty($data)) return null; 
    return explode($delim, $data);
}

function array_has_string_keys(array $array) {
    return count(array_filter(array_keys($array), 'is_string')) > 0;
}

function attr_isset($key, $val, $default) {
    return isset($key) && strlen($key) ? $val : $default;
}

function input_key_isset($arr, $key, $default = '', $val = '') {
    if (array_key_exists($key, $arr) && !empty($arr[$key])) {
        return strlen($val) ? $val : $arr[$key];
    } 
    return $default;
}

function set_extra_attrs($extra, $exclude = []) {
    $attrs = "";
    if (!empty($extra)) { 
        foreach ($extra as $attr => $value) {
            if ( ! in_array($attr, $exclude)) {
                $attrs .= "{$attr}='{$value}' ";
            }
        } 
    } 
    return $attrs;
}

function sort_array(array $arr, array $sort_order) {
    $sorted = [];
    foreach ($sort_order as $key) {
        if (isset($arr[$key])) {
            $sorted[$key] = $arr[$key];
        }
    }
    return $sorted;
}

function print_color($code, $name = '', $pos = 'right', $icon = 'square') {
    if ( ! strlen($code)) return;
    $color = '<i class="fa fa-'.$icon.'" style="color: '.$code.'"></i> ';
    //if name is not set, use only color
    if ( ! strlen($name)) return $color;
    $color = $pos == 'left' ? $color.' '.$name : $name.' '.$color;
    return $color;
}

function print_colors($codes, $names = '', $pos = 'left', $icon = 'square', $return = 'string') {
    if ( ! strlen($codes)) return;
    $colors_arr = [];
    //if names is not set, use only colors
    if ( ! strlen($names)) {
        $colors = split_us($codes);
        foreach ($colors as $code) {
            $colors_arr[] = print_color($code, '', $pos, $icon);
        }
        return $return == 'array' ? $colors_arr : join(' ', $colors_arr);
    } 
    $colors = array_combine(split_us($codes), split_us($names));
    foreach ($colors as $code => $name) {
        $colors_arr[] = print_color($code, $name, $pos, $icon);
    }
    return $return == 'array' ? $colors_arr : join(', ', $colors_arr);
}

function rating_stars($rating) {
    $diff = 5 - $rating; $rated = ''; $unrated = '';
    //rated
    for ($i = 0; $i < $rating; $i++) {
        $rated .= '<i class="fa fa-star"></i>'; 
    }
    //unrated
    if ($diff > 0) {
        for ($i = 0; $i < $diff; $i++) {
            $unrated .= '<i class="fa fa-star-o"></i>'; 
        }
    }
    return '<span class="rating">'.$rated.$unrated.'</span>';
}