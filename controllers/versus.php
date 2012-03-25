<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Versus extends CI_Controller {

	public function index()
	{
		$this->load->model('Images_model', 'image');
		$data['left'] = $this->image->random();
		$data['right'] = $this->image->random();
		
		$this->load->view('versus', $data);
	}
}

/* End of file image.php */
/* Location: ./application/controllers/image.php */