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

	public function sign_up()
	{
		if($this->auth->is_logged_in())
		{
			$this->session->set_flashdata('notice', 'You already have an account');
			redirect('/');
		}

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

	public function sign_out()
	{
		if($this->auth->is_logged_in())
		{
			$this->auth->sign_out();
			$this->session->set_flashdata('notice', 'Logged out');
		}
		redirect('/user/sign_in');
	}

	public function table($orderby = 'rank')
	{
		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}

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

	public function profile($username = FALSE)
	{
		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}

		$this->load->model('users_model', 'user');
		$this->load->library('markdown');
		$this->load->helper(array('date', 'inflector', 'number', 'image_justifaction'));

		$user_id = $this->session->userdata('user_id');
		if($username AND $user = $this->user->get_user_by_name($username) )
		{
			$img = $this->user->get_user_images_info($user['id']);
		}
		else
		{
			show_404();
		}

		$markdown_source = $this->user->profile($user['id']);
		$html = $this->markdown->transform($markdown_source);

		$faves = $this->user->get_faves($user_id);
		if($faves === FALSE)
		{
			$data['rows'] = 'User does not have any faves';
		}
		else
		{
			$data['rows'] = build_gallery($faves, 1170, 6);
		}
		$data['heading'] = 'Faves';
		$faves = $this->load->view('common/gallery', $data, TRUE);
		$vars = array('{username}', '{title}', '{timeframe}', '{since}', '{userid}', '{karma}', '{rankth}', '{rank}', '{imgcount}', '{imgsize}', '{faves}');

		$values = array(ucfirst($user['name']), $user['title'], timeframe($user['created']), $user['created'], $user['id'], $user['karma'], ordinal_suffix($user['rank']), $user['rank'], $img['img_count'], byte_format($img['img_size']), $faves);

		$html = str_replace($vars, $values, $html);

		$this->load->view('include/header');
		$this->load->view('include/nav');
		$this->load->view('common/echo', array('echo_this' => $html));
		if($user_id == $user['id'])
		{
			$this->load->view('user/modify-profile-button');
		}
		$this->load->view('include/footer');

	}

	public function invite()
	{
		if( ! $this->auth->is_autoconfirmed())
		{
			$this->session->set_flashdata('notice', 'You need to have a higher rank to access this page');
			redirect('/');
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

	public function modify($section = FALSE)
	{
		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}
		$user_id = $this->session->userdata('user_id');
		$this->load->model('users_model', 'user');
		$data['markdown'] = $this->user->profile($user_id);

		if( ! $section)
		{
			//Show the form to the user
			$this->load->view('include/header');
			$this->load->view('include/nav');
			$this->load->view('user/modify-profile', $data);
			$this->load->view('include/footer');
		}
		elseif($section == 'password')
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('passconf', 'Password confirmation', 'matches[passnew]');
			if($this->form_validation->run() == FALSE)
			{
				//Show the form again
				$this->load->view('include/header');
				$this->load->view('include/nav');
				$this->load->view('user/modify-profile', $data);
				$this->load->view('include/footer');
			}
			else
			{
				$user = $this->user->get_user_by_id($user_id);
				if($user['passhash'] == sha1($this->input->post('password')))
				{
					//Insert the new password in the database
					$this->user->change_password($user_id, sha1($this->input->post('passnew')));
					$this->session->set_flashdata('success', 'New password has been set');
					redirect('/user/modify');
				}
				$this->session->set_flashdata('error', 'Wrong password');
				redirect('/user/modify');
			}
		}
		elseif($section == 'profile')
		{
			if(trim($this->input->post('markdown')) != '')
			{
				//Update the user profile
				$this->user->change_profile($user_id, $this->input->post('markdown'));
				$this->session->set_flashdata('success', 'Profile updated');
				redirect('/user/profile/' . $this->session->userdata('username'));
			}
			$this->session->set_flashdata('warning', 'Your profile can\'t be empty');
			redirect('/user/modify');
		}
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */