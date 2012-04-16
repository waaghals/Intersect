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
				if($this->auth->sign_in($this->input->post('username'), $this->input->post('password')))
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
			//Show the sign in form
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

			$this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.name]');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
			$this->form_validation->set_rules('key', 'Key', 'required|exact_length[10]|alpha');

			if($this->form_validation->run() == FALSE)
			{
				$this->load->view('include/header');
				$this->load->view('include/nav');
				$this->load->view('user/sign_up_form');
				$this->load->view('include/footer');
			}
			else
			{
				$this->load->model('invite_model', 'invite');
				$inviter = $this->invite->is_valid($this->input->post('key'));
				if($inviter === FALSE)
				{
					$this->session->set_flashdata('error', 'Key is invalid, It might have been expired.');
					redirect('/user/sign_up');
				}
				else
				{
					$this->auth->create_user($this->input->post('username'), $this->input->post('password'));
					$this->session->set_flashdata('success', 'Account created, ' . $inviter . ' welcomes you!');
					redirect('/user/sign_in');
				}
				
			}
		}
		else
		{
			//Show the sign in form
			$this->load->view('include/header');
			$this->load->view('include/nav');
			$this->load->view('user/sign_up_form');
			$this->load->view('include/footer');
		}
	}

	public function sign_out()
	{
		$this->auth->sign_out();
		$this->session->set_flashdata('notice', 'Logged out');
		redirect('/');
	}

	public function table($orderby = 'rank')
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

	public function invite()
	{
		if( ! $this->auth->is_autoconfirmed())
		{
			show_error('Not enough permissions');
		}
		$this->load->model('invite_model', 'invite');
		$data['numkeys'] = $this->invite->count_keys($this->session->userdata('user_id'));
		if($data['numkeys'] >= 5)
		{
			$data['key'] = '**********';
		}
		else
		{
			$data['key'] = $this->invite->generate_key();
			$this->invite->save_key($data['key']);
		}
		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('user/invite', $data);
		$this->load->view('include/footer');
	}

}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
