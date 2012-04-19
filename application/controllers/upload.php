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
		$config['upload_path'] 		= APPPATH . 'tmp/';
		$config['allowed_types'] 	= 'gif|jpg|png';
		$config['max_size']			= '20480'; //20MB
		$config['max_width'] 		= '6000';
		$config['max_height']  		= '6000';
		$config['overwrite'] 		= FALSE;
		
		$this->load->library('upload');
		$this->load->model('process_model', 'process');
		
		if($this->cache->get('vacuuming') === FALSE)
		{
			//Not 'vacuuming', free to upload files
			$j = 0; $k = 0;
			for($i = 0; $i < count($_FILES['files']['name']); $i++)
			{
				$_FILES['field_name']['name'] 		= $_FILES['files']['name'][$i];
				$_FILES['field_name']['type'] 		= $_FILES['files']['type'][$i];
				$_FILES['field_name']['tmp_name'] 	= $_FILES['files']['tmp_name'][$i];
				$_FILES['field_name']['error'] 		= $_FILES['files']['error'][$i];
				$_FILES['field_name']['size'] 		= $_FILES['files']['size'][$i];
	
				$this->upload->initialize($config);
	
				if($this->upload->do_upload('field_name'))
				{
					//Try and process the file; insert in to database and move the uploaded file
					if($this->process->image($this->upload->data()))
					{
						$j++;
					}
					$k++;
				}
			}

			//Redirect the user back
			if($j > 0)
			{
				$this->session->set_flashdata('success', $j . ' Image(s) uploaded');
			}
			
			$dubs = $k - $j;
			if($dubs > 0)
			{
				$this->session->set_flashdata('warning', $dubs . ' Duplicate files have been ignored');
			}
			
			$this->session->set_flashdata('error', $this->upload->display_errors());
			redirect('/');
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