<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Page extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			redirect('/user/sign_in');
		}
	}

	public function show($slug)
	{
		$this->load->library('markdown');
		$this->load->model('page_model', 'page');
		if( ! $page = $this->page->load($slug))
		{
			show_404();
		}
		else
		{
			$data['echo_this'] = $this->markdown->transform($page['content']);
			$this->load->view('include/header', $page);
			$this->load->view('include/nav');
			$this->load->view('echo', $data);
			if($this->auth->is_admin())
			{
				$this->load->view('page/modify-page-button');
			}
			$this->load->view('include/footer');
		}
	}
	
	public function add()
	{
		if( ! $this->auth->is_admin())
		{
			show_404();
		}
		show_error('ToDo');
	}
	
	public function modify($slug)
	{
		if( ! $this->auth->is_admin())
		{
			show_404();
		}
		show_error('ToDo');
	}

}

/* End of file page.php */
/* Location: ./application/controllers/page.php */