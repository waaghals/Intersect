<?php
if ( ! function_exists('path_to_image')) {
    function path_to_image($img_id) {
    	$CI =& get_instance();
		
					
    	$parts = str_split($img_id);
		array_splice($parts, 4);
		
		//Add the base dir in front of the array items
		array_unshift($parts, $CI->config->item('img_dir'));
		
		$path = implode('/', $parts);
		return $path . '/';
    }
}