<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Auth {
	private $error = array();

	function __construct()
	{
		$this->ci = &get_instance();
		$this->ci->load->model('users_model', 'users');
	}

	function login($username, $password)
	{
		if($user = $this->ci->users->get_user_by_name($username))
		{
			if(sha1($password) == $user->passhash)
			{
				$this->ci->session->set_userdata(array('user_id' => $user->id, 'username' => $user->name, 'percentile' => $user->percentile));
				return TRUE;
			}
		}
		var_dump($user); exit;
		show_error('Username of password is incorrect.');
		return FALSE;
	}

	function logout()
	{
		// See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next
		// line
		$this->ci->session->set_userdata(array('user_id' => '', 'username' => '', 'percentile' => ''));

		$this->ci->session->sess_destroy();
	}

	function is_logged_in()
	{
		return $this->ci->session->userdata('user_id') > 0;
	}
	
	function is_banned()
	{
		//Rank above private
		return $this->ci->session->userdata('percentile') < 2;
	}
	
	function is_allowed()
	{
		//Rank above private
		return $this->ci->session->userdata('percentile') > 2;
	}

	function is_mod()
	{
		//Above Colonel is moderator
		return $this->ci->session->userdata('percentile') > 85;
	}

	
	function is_admin()
	{
		//Only generals are admins
		return $this->ci->session->userdata('percentile') > 99;
	}

	function get_user_id()
	{
		return (int)$this->ci->session->userdata('user_id');
	}

	function get_username()
	{
		return $this->ci->session->userdata('username');
	}

	function create_user($username, $password)
	{
		if( ! $this->is_username_valid($username))
		{
			show_error('Username already taken');
			return FALSE;
		}
		else
		{
			$user_id = $this->ci->users->create_user($username, sha1($password));
			if( ! $user_id)
			{
				show_error('Could not create user, Database error');
				return FALSE;
			}
			return $user_id;
		}
	}

	function is_username_valid($username)
	{
		return ((strlen($username) > 0) AND $this->ci->users->is_username_available($username));
	}

	function change_password($old_pass, $new_pass)
	{
		$user_id = $this->ci->session->userdata('user_id');

		if($user = $this->ci->users->get_user($user_id))
		{
			if(sha1($password) == $user->passhash)
			{
				$this->ci->users->change_password($user_id, $passhash);
				return TRUE;
			}
			show_error('Original password is incorrect');
		}
		return FALSE;
	}

	function delete_user($password)
	{
		$user_id = $this->ci->session->userdata('user_id');

		if($user = $this->ci->users->get_user($user_id))
		{
			if(sha1($password) == $user->passhash)
			{
				$this->ci->users->delete_user($user_id);
				$this->logout();
				return TRUE;
			}
			show_error('Password is incorrect, account NOT deleted');
		}
		return FALSE;
	}

}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */