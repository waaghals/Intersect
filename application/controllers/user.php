<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('auth');
		$this->load->helper(array('url', 'form'));
	}

	public function index()
	{
		if($this->auth->is_logged_in())
		{
			$this->session->set_flashdata('notice', 'Nothing to do here');
			redirect('/');
		}
		$this->sign_in();
	}

	public function sign_in()
	{
		if($this->auth->is_logged_in())
		{
			$this->session->set_flashdata('notice', 'You are already logged in');
			redirect('/');
		}

		if($this->input->post('submit'))
		{
			//Form submitted
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if($this->form_validation->run() == FALSE)
			{
				$this->load->view('include/header');
				$this->load->view('include/nav');
				$this->load->view('user/sign_in_form');
				$this->load->view('include/footer');
				
				$this->session->keep_flashdata('redirect');
			}
			else
			{
				if($this->auth->login($this->input->post('username'), $this->input->post('password')))
				{
					$this->load->model('users_model', 'users');
					$this->config->load('karma');
					$this->users->add_karma($this->session->userdata('user_id'), $this->config->item('sign_in_karma'));
					
					if($redirect = $this->session->flashdata('redirect'))
					{
						redirect($redirect);
					}
					redirect('/');
				}
				else
				{
					$this->load->view('include/header');
					$this->load->view('include/nav');
					$this->load->view('user/sign_in_form');
					$this->load->view('include/footer');
					$this->session->keep_flashdata('redirect');
				}
			}
		}
		else
		{
			//Show the login form
			$this->load->view('include/header');
			$this->load->view('include/nav');
			$this->load->view('user/sign_in_form');
			$this->load->view('include/footer');
			$this->session->keep_flashdata('redirect');
		}

	}

	public function sign_up()
	{
		if($this->auth->is_logged_in())
		{
			$this->session->set_flashdata('notice', 'You already have an account');
			redirect('/');
		}

		if($this->input->post('submit'))
		{
			//Form submitted
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');

			if($this->form_validation->run() == FALSE)
			{
				$this->load->view('user/sign_up_form');
			}
			else
			{
				if($this->input->post('password') == $this->input->post('passconf'))
				{
					if(is_numeric($this->auth->create_user($this->input->post('username'), $this->input->post('password'))))
					{
						$this->session->set_flashdata('success', 'Account created');
						redirect('/');
					}
					else
					{
						$this->load->view('include/header');
						$this->load->view('include/nav');
						$this->load->view('user/sign_up_form');
						$this->load->view('include/footer');
					}
				}
				else
				{
					show_error('Password did not match');
				}
			}
		}
		else
		{
			//Show the login form
			$this->load->view('include/header');
			$this->load->view('include/nav');
			$this->load->view('user/sign_up_form');
			$this->load->view('include/footer');
		}
	}

	public function logout()
	{
		$this->auth->logout();
		$this->session->set_flashdata('notice', 'Logged out');
		redirect('/');
	}
	
	public function table($orderby)
	{
		$this->load->library('table');
		$this->table->set_template(array('table_open' => '<table class="table">'));
		
		$this->load->model('users_model', 'users');
		$query = $this->users->user_data($orderby);
		
		$this->table->set_heading('<a href="/user/table/user_id">Id</a>', '<a href="/user/table/title">Title</a>', '<a href="/user/table/name">Name</a>', '<a href="/user/table/karma">Karma</a>', '<a href="/user/table/rank">Rank</a>');
		$data['table'] = $this->table->generate($query);
		
		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('user/table', $data);
		$this->load->view('include/footer');
		
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */