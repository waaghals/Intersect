<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Users_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	function get_user_by_id($user_id)
	{
		$this->load->driver('cache', array('adapter' => 'file'));

		if( ! $rows = $this->cache->get('quantiles'))
		{
			$this->load->model('images_model', 'images');
			$this->images->calc_quantiles(10);

			//Don't do this recursively, if the query fails there will be a infinite loop
			if( ! $rows = $this->cache->get('quantiles'))
			{
				show_error('Could not get query result from cache.');
				return FALSE;
			}
		}
		//Only use the first row
		$quantile = $rows[0]['metric'];

		//If the user has a level above 8 he cannot be auto banned.
		//Users with level above 5 don't 'expire'
		$sql = "SELECT 
					u.id, 
					name, 
					IF(level > 5, level, IF(DATEDIFF(expiration,NOW()) > 0, level, 1)) AS level,
					LOWER(HEX(passhash)) AS passhash, 
					IF(level > 8, FALSE, IF(AVG(i.rating) < ?, TRUE, FALSE)) AS banned
					FROM user AS u
					JOIN user_image AS ui
						ON u.id = ui.user_id
					JOIN image AS i 
						ON i.id = ui.image_id
					WHERE u.id = ?";
		$query = $this->db->query($sql, array($quantile, $user_id));
		return $query->row();
	}

	function get_user_by_name($username)
	{
		$sql = "SELECT id FROM user WHERE name = ?";
		$query = $this->db->query($sql, $username);
		$row = $query->row_array();
		if($query->num_rows() == 1)
		{
			return $this->get_user_by_id($row['id']);
		}
		return FALSE;
	}

	function is_username_available($username)
	{
		$sql = "SELECT 1 FROM user WHERE name = ?";
		$query = $this->db->query($sql, $username);
		return $query->num_rows() == 0;
	}

	function create_user($username, $passhash)
	{
		$this->db->set('passhash', 'UNHEX(\'' . $passhash . '\')', FALSE);
		$this->db->set('name', $username);
		$this->db->set('created', date('Y-m-d H:i:s'));
		$this->db->set('expiration', date('Y-m-d H:i:s'));
		if($this->db->insert('user'))
		{
			//Return the user_id
			return $this->db->insert_id();
		}
		return FALSE;
	}

	function delete_user($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete('user');
		if($this->db->affected_rows() > 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	function change_password($user_id, $passhash)
	{
		$this->db->set('password', $passhash);
		$this->db->where('id', $user_id);

		$this->db->update('user');
		return $this->db->affected_rows() > 0;
	}

	function add_time($interval)
	{
		$column = "IF(expiration >= NOW(),
					DATE_ADD(expiration, INTERVAL " . $interval . "),
					DATE_ADD(NOW(), INTERVAL " . $interval . "))";
		$this->db->set('expiration', $column, FALSE);
		$this->db->where('id', $this->auth->get_user_id(), FALSE);
		$this->db->update('user');
	}

}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */