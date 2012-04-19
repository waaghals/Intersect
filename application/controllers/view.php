<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class View extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		if( ! $this->auth->is_allowed())
		{
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/upload');
		}
	}

	public function image($id)
	{
		$this->load->model('tag_model', 'tags');
		$data['tags'] = $this->tags->for_image($id);
		$this->load->helper('path');
		$data['path'] = path_to_image($id) . $id;
		$data['id'] = $id;
		
		$this->load->view('include/header.php');
		$this->load->view('include/nav.php');
		$this->load->view('view/image.php', $data);
		$this->load->view('include/footer.php');
	}

	public function top($number = 500)
	{
		$this->load->model('images_model', 'images');
		$this->load->helper('image_justifaction_helper');

		$rows = build_gallery($this->images->best($number), 1170);
		$data['rows'] = $rows;
		
		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('gallery', $data);
		$this->load->view('include/footer');
	}
}