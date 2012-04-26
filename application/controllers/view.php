<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class View extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}
	}

	public function image($id)
	{
		$this->load->model('tag_model', 'tags');
		$this->load->model('images_model', 'images');
		$this->config->load('points');
		
		$data['tags'] = $this->tags->for_image($id);
		$this->load->helper('path');
		$data['path'] = path_to_image($id) . $id;
		$data['id'] = $id;
		
		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('view/image', $data);
		$this->load->view('include/footer');
		
		$this->images->add_points($id, $this->config->item('view_points'));
	}
	
	public function gallery($type = 'all', $number = 100)
	{
		$this->_gallery($type, FALSE, $number);
	}

	public function top($number = 100)
	{
		$this->_gallery('top', FALSE, $number);
	}
	
	public function flop($number = 100)
	{
		$this->_gallery('flop', FALSE, $number);
	}
	
	public function trending()
	{
		$this->_gallery('trending', FALSE);
	}
	
	private function _gallery($what = 'all', $include = FALSE, $number = 100)
	{
		if( ! $include)
		{
			$this->load->view('include/header');
			$this->load->view('include/nav');
		}
		
		if($what == 'all')
		{
			$this->_gallery('trending', TRUE, $number);
			$this->_gallery('top', TRUE, $number);
			$this->_gallery('flop', TRUE, $number);
		}
		else
		{
			$this->load->model('images_model', 'images');
			$this->load->helper('image_justifaction_helper');
	
			$rows = build_gallery($this->images->$what($number), 1170);
			$data['rows'] = $rows;
			$data['heading'] = ucfirst($what);
			
			$this->load->view('common/gallery', $data);
		}
		
		if( ! $include)
		{
			$this->load->view('include/footer');
		}
	}
	
	public function random()
	{
		$this->load->model('images_model', 'images');
		$id = $this->images->random();
		$this->image($id);
	}
}