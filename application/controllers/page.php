<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Page extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('page_model', 'page');
		$this->load->helper('url');
		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}
	}

	public function show($slug)
	{
		$this->load->library('markdown');
		if( ! $page = $this->page->load($slug))
		{
			show_404();
		}
		else
		{
			$data['echo_this'] = $this->markdown->transform($page['markdown']);
			$this->load->view('include/header', $page);
			$this->load->view('include/nav');
			$this->load->view('echo', $data);
			if($this->auth->is_autoconfirmed())
			{
				$this->load->view('page/modify-page-button');
			}
			$this->load->view('include/footer');
		}
	}
	
	public function add()
	{
		if( ! $this->auth->is_autoconfirmed())
		{
			show_404();
		}
		
		$this->load->library(array('form_validation'));
 
        //set validation rules
        $this->form_validation->set_rules('slug', 'Slug', 'required|xss_clean|max_length[20]|unique[page.slug]');
        $this->form_validation->set_rules('title', 'Title', 'required|xss_clean');
		$this->form_validation->set_rules('markdown', 'content', 'required');
 
        if ($this->form_validation->run() == FALSE)
        {
        	$this->load->view('include/header');
			$this->load->view('include/nav');
            $this->load->view('page/add');
			$this->load->view('include/footer');
        }
        else
        {
            $slug = url_title($this->input->post('slug'));
            $title = $this->input->post('title');
			$content = $this->input->post('markdown');
            if($this->page->add($slug, $title, $content))
			{
				$this->session->set_flashdata('success', 'Page created');
            	redirect('page/show/' . $slug);
			}
			show_error('Unexpected DB error');
            
        }
	}
	
	public function modify($slug)
	{
		if( ! $this->auth->is_autoconfirmed())
		{
			show_404();
		}
		
		if( ! $data = $this->page->load($slug))
		{
			show_error('No page for slug: ' . $slug);
		}
		
		$this->load->library(array('form_validation'));
 
        //set validation rules
		$this->form_validation->set_rules('markdown', 'content', 'required');
		$this->form_validation->set_rules('comment', 'comment', 'required|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
        	$this->load->view('include/header');
			$this->load->view('include/nav');
            $this->load->view('page/modify', $data);
			$this->load->view('include/footer');
        }
        else
        {
			$content = $this->input->post('markdown');
			$comment = $this->input->post('comment');
            if($this->page->modify($slug, $content, $comment))
			{
				$this->session->set_flashdata('success', 'Page updated');
            	redirect('page/show/' . $slug);
			}
			show_error('Unexpected DB error');
            
        }
	}

}

/* End of file page.php */
/* Location: ./application/controllers/page.php */