<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {

	public function image($id) {
		$this->load->model('image_model', 'images');
		$img = $this->image->get_image($id);
		$this->load->view('image', $img);
	}
}