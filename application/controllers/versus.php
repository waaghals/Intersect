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
		
		//If queue is empty a random is is taken
		$data['left'] = $this->images->get_data($this->images->from_queue());
		$data['right'] = $this->images->get_data($this->images->random());

		if($data['left']['id'] == $data['right']['id'])
		{
			$this->index();
			return;
		}
		
		$this->load->helper('form');
		
		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('versus', $data);
		$this->load->view('include/footer');
		return;
	}
}

/* End of file image.php */
/* Location: ./application/controllers/image.php */