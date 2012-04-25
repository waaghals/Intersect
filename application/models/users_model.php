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
					passhash,
					salt,
					title,
					created,
					karma,
					rank
					FROM user AS u
					JOIN user_data AS ud
						ON(ud.user_id = u.id)
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
	
	function get_user_images_info($user_id)
	{
		$sql = "SELECT 
					COUNT(id.image_id) AS img_count,
					SUM(id.size)*1024 AS img_size,
					AVG(rating) AS rating
					FROM image_data AS id
					JOIN image AS i
						ON(i.id = id.image_id)
					JOIN user_image AS ui
						ON(ui.image_id = id.image_id)
					WHERE ui.user_id= ?";
		$query = $this->db->query($sql, $user_id);
		if($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		return FALSE;
	}

	function create_user($username, $password)
	{
		$a = $this->hashed($password);
		
		$this->db->set('passhash', $a['hash']);
		$this->db->set('name', $username);
		$this->db->set('salt', $a['salt']);
		$this->db->set('created', date('Y-m-d H:i:s'));
		if($this->db->insert('user'))
		{
			$user_id = $this->db->insert_id();
			
			$this->add_karma($user_id, 100);
			$this->update_data_table();

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

	function change_password($user_id, $password)
	{
		$a = $this->hashed($password);
		
		$this->db->set('passhash', $a['hash']);
		$this->db->set('salt', $a['salt']);
		$this->db->where('id', $user_id);

		$this->db->update('user');
		return $this->db->affected_rows() > 0;
	}
	
	function change_profile($user_id, $markdown)
	{
		$rev_id = $this->create_revision($markdown);
		
		$sql = "INSERT INTO profile (user_id, latest_rev_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE latest_rev_id = ?";
        $query = $this->db->query($sql, array($user_id, $rev_id, $rev_id));
		if($this->db->affected_rows() > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	private function create_revision($markdown)
	{
		$user_id = $this->session->userdata('user_id');
    	$sql = "INSERT INTO revisions (user_id, markdown, modified, comment) VALUES (?, ?, ?, ?)";
		$query = $this->db->query($sql, array($user_id, $markdown, date("Y-m-d H:i:s"), 'Profile update'));
		if($this->db->affected_rows() == 1)
		{
			return $this->db->insert_id();
		}
		show_error('Could not create revision');
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
				ON DUPLICATE KEY UPDATE title=values(title), percentile=values(percentile), karma=values(karma), rank=values(rank)";
		return $this->db->query($sql);
	}
	
	public function user_data($orderby = 'rank')
	{
		if( ! in_array($orderby, array('user_id', 'name', 'title', 'karma', 'rank')))
		{
			show_error('Not a valid column for order by');
		}

		$sql = "SELECT user_id, title, name, karma, rank FROM user_data JOIN user ON user.id = user_data.user_id ORDER BY " . $orderby;
		$query = $this->db->query($sql);
		if($query->num_rows() > 0)
		{
			return $query;
		}
		return FALSE;
	}
	
	public function profile($user_id)
	{
		$sql = "SELECT markdown FROM profile AS p JOIN revisions AS r ON (p.latest_rev_id = r.id) WHERE p.user_id = ?";
		$query = $this->db->query($sql, $user_id);

		if($query->num_rows() == 1)
		{
			$row = $query->row_array();
			return $row['markdown'];
		}
		return <<<MARKDOWN
#_{title}_ {username}
Hello I'm {username} and I am here for over {timeframe}.
MARKDOWN;
	}
	
	public function add_fav($user_id, $img_id)
	{
		$sql = 'INSERT IGNORE INTO user_fav (user_id, image_id, added) VALUES (?, ?, ?)';
		$this->db->query($sql, array($user_id, $img_id, date('Y-m-d H:i:s')));
		
		$this->load->model('images_model', 'images');
		$this->config->load('points');
		$this->images->add_points($img_id, $this->config->item('fav_points'));
		if($this->db->affected_rows() > 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	public function get_faves($user_id)
	{
		$sql = "
			SELECT 
				i.id, 
				d.width * d.height / 150000 + i.rating AS rating,
				d.width AS width,
				d.height AS height,
				ROUND(250 / d.height * d.width) AS twidth,
				250 AS theight,
				NULL AS vwidth
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
				JOIN user_fav AS f
					ON ( i.id = f.image_id)
			WHERE f.user_id = ?
			ORDER BY f.added DESC";

		$query = $this->db->query($sql , $user_id);
		if($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		return FALSE;
	}
	
	private function hashed($pass)
	{
	    $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex
	    $hash = hash("sha256", $salt . $pass); 
	    return array('hash' => $hash, 'salt' => $salt);
	}
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */