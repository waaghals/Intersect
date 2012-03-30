<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {

	public function image($id) {
		$this->load->model('image_model', 'images');
		$img = $this->image->get_image($id);
		$this->load->view('image', $img);
	}
	
	public function top($number = 500) {
		$this->load->model('images_model', 'images');
		
		foreach($this->images->best($number) as $image) {
			$data = array('path' => $image->path, 'id' => $image->id);
			$this->load->view('image', $data);
		}
		$this->load->view('tracker');
	}
	
	public function percentile() {
		$this->load->model('images_model', 'images');
		
		foreach($this->images->percentile() as $image) {
			$data = array('path' => $image->path, 'id' => $image->id);
			$this->load->view('image', $data);
		}
		$this->load->view('tracker');
	}
}