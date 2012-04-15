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
		$sql = "SELECT 
					u.id,
					name,
					percentile,
					LOWER(HEX(passhash)) AS passhash,
					title
					FROM user AS u
					JOIN view_user_rank AS ur
						ON(ur.user_id = u.id)
					WHERE u.id = ?";
		$query = $this->db->query($sql, $user_id);
		if($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		return FALSE;
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
		if($this->db->insert('user'))
		{
			$user_id = $this->db->insert_id();
			
			$this->add_karma->($user_id, 100);
			return $user_id;
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

	function add_karma($user_id, $amount)
	{
		$data = array(
			'user_id' => $user_id,
			'karma' => $amount,
			'given' => date('Y-m-d H:i:s')
		);
		
		if($this->db->insert('karma', $data))
		{
			return TRUE;
		}
		return FALSE;
	}
	
	function update_data_table()
	{
		$sql = "INSERT INTO user_data 
				(SELECT * FROM (SELECT 
					uk.user_id as user_id,
					r.title AS title,
					kp.percentile AS percentile,
					uk.karma AS karma,
					FIND_IN_SET(uk.karma, kc.karma_concat) AS rank
				FROM vw_user_karma as uk
				JOIN vw_karma_percentile AS kp
					ON uk.karma = kp.karma
				LEFT JOIN rank AS r
					ON kp.percentile >= r.above_percentile
				JOIN vw_karma_concat AS kc
				ORDER BY user_id, r.above_percentile DESC) AS t 
				GROUP BY user_id)
				ON DUPLICATE KEY UPDATE title=values(title), percentile=values(percentile), karma=values(karma), rand=values(rank)";
		$query = $this->db->query($sql);
	}

}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */