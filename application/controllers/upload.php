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
			if( ! $uploads = $this->upload->do_multi_upload())
			{
				show_error($this->upload->display_errors());
			}
			else
			{
				//Upload successfull
				$this->load->model('process_model', 'process');

				//Try and process the file; insert in to database and move the uploaded file
				$i;
				foreach($uploads as $data)
				{
					$this->process->image($data);
					$i++;
				}
				
				/*
				$this->load->view('include/header');
				$this->load->view('include/nav');
				$this->load->view('upload/preview', array('part' => 'open'));
				foreach($uploads as $data)
				{
					$img_id = $this->process->image($data);
					$this->load->view('upload/thumbnail', array('id' => $img_id));
				}
				$this->load->view('upload/preview', array('part' => 'close'));
				$this->load->view('include/footer');
				
				
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
				*/
				//Redirect the user back
				$this->session->set_flashdata('success', $i . ' Image(s) uploaded');
				redirect('/');
			}
		}
		else
		{
			show_error('One moment, the upload folder is being vacuumed by our cleaning lady, please try again.');
		}
	}

	public function tag()
	{
		$i = 0;
		foreach($this->input->post('tags') as $img_id => $tag_str)
		{
			$tags = explode(',', $tag_str);
			$this->load->model('images_model', 'images');
			foreach($tags as $tag)
			{
				$this->images->add_tag($img_id, $tag);
				$i++;
			}
		}
		
		echo $i . ' tags saved';
	}

}

/* End of file upload.php */
/* Location: ./application/controllers/upload.php */