<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		
		if( ! $this->auth->is_allowed()) {
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			$this->session->set_flashdata('redirect', uri_string());
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
	
	public function similar($id) {

		$this->load->model('signature_model', 'signature');
		$row = $this->signature->get_image_info($id);
		
		$similar = $this->signature->find_similar_pictures($row['cvec'], TRUE);
		//$this->load->helper('path');
		//echo puzzle_fill_cvec_from_file(path_to_image($id).$id) . "<br >";
		//echo $sig;
		var_dump($similar);
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