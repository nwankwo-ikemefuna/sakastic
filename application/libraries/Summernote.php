<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Summernote {

	private $ci;

	public function __construct() {
		$this->ci =& get_instance();
	}


	public function upload() {
        $path = xpost('smt_path');
        $size = strlen(xpost('smt_size')) ? xpost('smt_size') : 500;
        //is upload path set?
        if ( ! strlen($path)) 
            json_response('Upload path not specified', false);
        $path = 'uploads/images/'.$path;
        $ext = 'jpg|jpeg|png|gif';
        $conf = ['path' => $path, 'ext' => 'jpg|jpeg|png|gif', 'size' => $size, 'required' => false];
        $upload = upload_file('smt_file', $conf);
        // pretty_print($upload); die;
        //file upload fails
        if ( ! $upload['status']) 
            json_response($upload['error'], false);
        //get the uploaded image src
        $src = base_url($path.'/'.$upload['file_name']);
        json_response($src);
    }


    public function delete() {
		$src = xpost('src');
		//get the relative image
		$file = str_replace(base_url(), '', $src);
		if (unlink_file($file)) {
			json_response('File deleted successfully');
		} 
		json_response('Unable to delete file', false);
    }


	/**
	 * get images from summernote textfield
	 * @param  [string] $content [the content of the summernote textfield]
	 * @return [array]
	 */
	public function extract($content) {
	    $dom = new domDocument;
	    $dom->loadHTML(html_entity_decode($content)); //preserve html tags
	    $dom->preserveWhiteSpace = false; //remove redundant white space
	    $imgs  = $dom->getElementsByTagName("img");
	    $img_len = $imgs->length;
	    $extracted = []; //available image names arr
	    for($i = 0; $i < $img_len; $i++) {
	        //image link in summernote textfield
	        $img_src = $imgs->item($i)->getAttribute("src");
	        //get image name only
	        $name = explode('/', $img_src);
	        $extracted[] = end($name);
	    }
	    return $extracted;
	}

	/**
	 * unlink removed images from summernote textfield
	 * This method should be called in the post handler for the data in the summernote field e.g. in a form handler
	 * @param  [string] $textarea_content [the content of the summernote textfield]
	 * @param  [string] $input_images [the uploaded image names in the textfield, in hidden input]
	 * @param  [string] $path [the absolute path to the image]
	 * @return [boolean]
	 */
	public function remove_files($textarea_content, $input_images, $path) {
	    $extracted = $this->extract($textarea_content);
	    $uploaded = explode('[***]', $input_images);
	    //get uploaded image files not found in summernote textfield when form is submitted
	    $removed = array_diff($uploaded, $extracted);
	    $count = 0;
	    if (empty($removed)) return true;
	    foreach($removed as $to_delete) {
	        $file_path = $path.'/'.$to_delete;
	        if (file_exists($file_path)) {
	            unlink($file_path);
	            $count++;
	        }
	    }
	    return ($count == count($removed));
	} 

	/**
	 * [summernote uploaded images hidden input]
	 * @return [html] 
	 */
	public function config($path, $size = 500, $name = 'smt_images', $content = '', $return = false) {
		$images = strlen($content) ? join("[***]", $this->extract($content)) : '';
	    $input = '<input type="hidden" class="smt_path" value="'.$path.'">';
	    $input .= '<input type="hidden" class="smt_size" value="'.$size.'">';
	    $input .= '<input type="hidden" name="'.$name.'" class="smt_images form-control" value="'.$images.'">';
	    if ($return) return $input;
	    echo $input;
	}

}