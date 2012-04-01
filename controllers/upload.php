<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index() {
		$this->load->view('upload/form', array('error' => ' ' ));
	}

	function process() {
		$this->load->library('upload');

		if ( ! $this->upload->do_upload()) {
			var_dump($this->upload->display_errors());
			//show_error('Failed to upload the file, please try again.');
		} else {
			//Upload successfull
			$this->load->model('process_model', 'process');
			
			//Try and process the file; insert in to database and move the uploaded file
			if( ! $this->process->image($this->upload->data())) {
				show_error('Something went wrong, please try again.');
			}
		}
	}
}

/* End of file upload.php */
/* Location: ./application/controllers/upload.php */