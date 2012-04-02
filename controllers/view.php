<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		
		if( ! $this->auth->is_allowed()) {
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			redirect('/upload');
		}
	}

	public function image($id) {
		$this->load->model('images_model', 'images');
		$img = $this->images->get_image($id);
		
		$this->load->model('tag_model', 'tags');
		$tags = $this->tags->for_image($id);
		
		$this->load->view('image', $img);
		$this->load->view('tags', array('tags' => $tags));
		$this->load->view('tracker');
	}

	public function top($number = 500, $container_width = 1024) {
		$this->load->model('images_model', 'images');
		$this->load->helper('image_justifaction_helper');
		
		$rows = build_gallery($this->images->best($number), $container_width);
		$data['rows'] = $rows;
		$data['container_width'] = $container_width;
		
		$this->load->view('gallery', $data);
		$this->load->view('tracker');
	}

	public function percentile($container_width = 1024) {
		$this->load->model('images_model', 'images');
		$this->load->helper('image_justifaction_helper');

		$rows = build_gallery($this->images->percentile(), $container_width);
		$data['rows'] = $rows;
		$data['container_width'] = $container_width;
		
		$this->load->view('gallery', $data);
		$this->load->view('tracker');
	}
}