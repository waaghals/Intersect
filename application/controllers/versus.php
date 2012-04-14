<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Versus extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		if( ! $this->auth->is_allowed())
		{
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			redirect('/upload');
		}
	}

	public function index()
	{
		$this->load->model('Images_model', 'images');
		$data['left'] = $this->images->from_queue();
		$data['right'] = $this->images->random();
		$data['left_id'] = array_pop(explode('/', $data['left']));
		$data['right_id'] = array_pop(explode('/', $data['right']));

		if($data['left_id'] == $data['right_id'])
		{
			$this->index();
			return;
		}

		$this->load->view('versus', $data);
		return;
	}

}

/* End of file image.php */
/* Location: ./application/controllers/image.php */