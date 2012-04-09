<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('build_row')) {
    function build_row(&$images, $total_width, $image_margin) {
        $row = array();
		$length = 0;

		while(count($images) > 0 && $length < $total_width) {
			$image =  array_shift($images);
			array_push($row, $image);
			
			$length += ($image['twidth'] + $image_margin * 2);
		}

		$delta = $length - $total_width;

		// if the line is too long, make images smaller
		if(count($row) > 0 && $delta > 0 ) {
			$row = trim_row($delta, $row);
		} else {
			//All images fit in the row, set twidth
			foreach($row as &$img) {
				$img['twidth'] = $img['twidth'];
			}
			unset($img);
 		}
		return $row;
    }
}
if ( ! function_exists('trim_row')) {
	function trim_row($delta, $row) {
		
		$num_img = count($row);
		$remainder = $delta % $num_img;
		$cut = ($delta - $remainder) / $num_img;

		foreach($row as &$image) {
			$image['vwidth'] = $image['twidth'] - $cut;
		}
		unset($image);

		if ($remainder != 0) {
			//Subtract the remainder of pixels to the first image
			$row[0]['vwidth'] = $row[0]['vwidth'] - $remainder;
		}
		
		return $row;
	}
	
}

if ( ! function_exists('build_gallery')) {
	function build_gallery($images, $width) {
			$rows = array();
			while(count($images) > 0) {
				array_push($rows, build_row($images, $width, '3'));
			}

			return $rows;
	}
}

if ( ! function_exists('calc_img_width')) {
	function calc_img_width($width, $height, $new_height) {
		$ratio = $new_height / $height;
		$new_width = $width * $ratio;
		return $new_width;
	}
}