<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Invite_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function generate_key()
	{
		$key = '';
		for($i = 0; $i < 10; $i++)
		{
			$key .= chr(mt_rand(ord('A'), ord('Z')));
		}
		return $key;
	}

	public function save_key($key)
	{
		if(strlen($key) != 10)
		{
			show_error('Invalid key length');
		}
		$this->db->insert('user_key', array('user_id' => $this->session->userdata('user_id'), 'key' => $key, 'created' => date('Y-m-d H:i:s')));
	}

	public function count_keys($user_id)
	{
		$sql = 'SELECT COUNT(0) AS count FROM user_key WHERE user_id = ?  AND created > (NOW() - INTERVAL 1 MONTH)';
		$query = $this->db->query($sql, $user_id);

		if($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row['count'];
		}
		return FALSE;
	}
	
	public function is_valid($key)
	{
		$sql = 'SELECT name FROM user_key LEFT JOIN user ON user.id = user_key.user_id WHERE `key` = ?  AND user_key.created > (NOW() - INTERVAL 1 MONTH)';
		$query = $this->db->query($sql, $key);

		if($query->num_rows() == 1)
		{
			$row = $query->row_array();
			$this->remove_key($key);
			return (is_null($row['name'])) ? 'Someone' : $row['name'];
		}
		return FALSE;
	}
	
	public function remove_key($key)
	{
		return $this->db->delete('user_key', array('key' => $key));
	}

}

/* End of file invite_model.php */
/* Location: ./application/models/auth/invite_model.php */
