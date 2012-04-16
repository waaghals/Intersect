<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->driver('cache', array('adapter' => 'file'));

		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->keep_flashdata('redirect');
			redirect('/user/sign_in');
		}
	}

	function index()
	{
		if($this->cache->get('vacuuming') === FALSE)
		{
			$this->load->view('include/header');
			$this->load->view('include/nav');
			$this->load->view('upload/form');
			$this->load->view('include/footer');
		}
		else
		{
			show_error('One moment, the upload folder is being vacuumed by our cleaning lady, please try again.');
		}
	}

	function process()
	{
		$this->load->library('upload');
		if($this->cache->get('vacuuming') === FALSE)
		{

			//Not 'vacuuming', free to upload files
			if( ! $this->upload->do_upload())
			{
				show_error('Failed to upload the file, please try again.');
			}
			else
			{
				//Upload successfull
				$this->load->model('process_model', 'process');

				//Try and process the file; insert in to database and move the uploaded file
				$image_id = $this->process->image($this->upload->data());
				if( ! $image_id)
				{
					show_error('Something went wrong, please try again.');
				}
				
				//Add the tags
				$tags = explode(',', $this->input->post('tags'));
				$this->load->model('images_model', 'images');
				foreach($tags as $tag)
				{
					$this->images->add_tag($image_id, $tag);
				}
				
				//Add the karma
				$this->load->model('users_model', 'users');
				$this->config->load('karma');
				$this->users->add_karma($this->session->userdata('user_id'), $this->config->item('upload_karma'));
				
				//Redirect the user back
				$this->session->set_flashdata('success', 'Image uploaded');
				if($redirect = $this->session->flashdata('redirect'))
				{
					redirect($redirect);
				}
				redirect('/');
			}
		}
		else
		{
			show_error('One moment, the upload folder is being vacuumed by our cleaning lady, please try again.');
		}
	}

}

/* End of file upload.php */
/* Location: ./application/controllers/upload.php */