<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Best extends CI_Controller {

	public function best($number = 500) {
		$this->load->model('image_model', 'images');
		
		foreach($this->images->best($number) as $image) {
			$data = array('path' => $image->path, 'id' => $image->id);
			$this->load->view('image', $data);
		}
		$this->load->view('tracker');
	}
	
	public function percentile() {
		$this->load->model('image_model', 'images');
		
		foreach($this->images->percentile() as $image) {
			$data = array('path' => $image->path, 'id' => $image->id);
			$this->load->view('image', $data);
		}
		$this->load->view('tracker');
	}
}

/* End of file best.php */
/* Location: ./application/controllers/best.php */