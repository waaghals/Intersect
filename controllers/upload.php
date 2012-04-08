<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->driver('cache', array('adapter' => 'file'));
		
		if( ! $this->auth->is_logged_in()) {
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->keep_flashdata('redirect');
			redirect('/user/sign_in');
		}
		$this->load->view('flash');
	}

	function index() {
		if ($this->cache->get('vacuuming') === FALSE) {
			$this->load->view('upload/form');
		} else {
			show_error('One moment, the upload folder is being vacuumed by our cleaning lady, please try again.');
		}
	}

	function process() {
		$this->load->library('upload');
		if ($this->cache->get('vacuuming') === FALSE) {
			
			//Not 'vacuuming', free to upload files
			if ( ! $this->upload->do_upload()) {
			show_error('Failed to upload the file, please try again.');
			} else {
				//Upload successfull
				$this->load->model('process_model', 'process');
				
				//Try and process the file; insert in to database and move the uploaded file
				if( ! $this->process->image($this->upload->data())) {
					show_error('Something went wrong, please try again.');
				}
				
				//Update the user expire time by 24 hours
				$this->load->model('users_model', 'users');
				$this->users->add_time('24 HOUR');
				$this->session->set_flashdata('success', 'Image uploaded');
				if($redirect = $this->session->flashdata('redirect')) {
					redirect($redirect);
				}
				redirect('/');
			}
		} else {
			show_error('One moment, the upload folder is being vacuumed by our cleaning lady, please try again.');
		}
	}
}

/* End of file upload.php */
/* Location: ./application/controllers/upload.php */