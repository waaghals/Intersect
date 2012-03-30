<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {

	public function image($id) {
		$this->load->model('image_model', 'images');
		$img = $this->image->get_image($id);
		$this->load->view('image', $img);
	}
	
	public function top($number = 500, $container_width = 1024) {
		$this->load->model('images_model', 'images');
		$this->load->helper('image_justifaction_helper');
		
		$rows = build_gallery($this->images->best($number), $container_width);
		
		foreach($rows as $row) {
			foreach($row as $image) {
				echo $this->load->view('resized_image', $image, true);
			}
			echo '<br />';
		}
		$this->load->view('tracker');
	}
	
	public function percentile($container_width = 1024) {
		$this->load->model('images_model', 'images');
		$this->load->helper('image_justifaction_helper');
		
		$rows = build_gallery($this->images->percentile(), $container_width);
		
		foreach($rows as $row) {
			foreach($row as $image) {
				echo $this->load->view('resized_image', $image, true);
			}
			echo '<br />';
		}
		$this->load->view('tracker');
	}
}