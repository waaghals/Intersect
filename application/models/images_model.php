<?php
class Images_model extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->helper('path', 'url');
	}

	public function top($number)
	{
		return $this->images($number, FALSE);
	}

	public function flop($number)
	{
		return $this->images($number, TRUE);
	}

	private function images($number, $worst)
	{
		if( ! is_numeric($number))
		{
			show_error('Number of images is not a valid number.');
			return;
		}
		//Order asc when worst, desc when best
		$order = ($worst) ? 'ASC' : 'DESC';

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
			ORDER BY rating " . $order . "
			LIMIT 0 , " . $number;

		$q = $this->db->query($sql);
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}
	
	public function trending()
	{
		$sql = "
SELECT a * LOG10(ABS(b))               metric, 
       d.image_id                      id, 
       d.width                         width, 
       d.height                        height, 
       ROUND(250 / d.height * d.width) twidth, 
       250                             theight, 
       NULL                            vwidth 
FROM   (SELECT ( ( COUNT(image_id) * SUM(x * y) ) - ( SUM(x) * SUM(y) ) ) / ( ( 
                              COUNT(image_id) * SUM(POWER(x, 2)) ) - 
               POWER(SUM(x), 2) ) 
                      AS `a`, 
               AVG(y) - ( ( COUNT(*) * SUM(x * y) ) - ( SUM(x) * SUM(y) ) ) / ( 
                        ( 
                                 COUNT(image_id) * SUM(POWER(x, 2)) ) - 
                        POWER(SUM(x), 2) ) * 
                        AVG(x) 
                      AS `b`, 
               image_id 
        FROM   (SELECT image_id, 
                       31 - DATEDIFF(CURDATE(), given) x, 
                       SUM(points)                     y 
                FROM   `image_point` 
                GROUP  BY image_id, 
                          given 
                HAVING x <= 31) t 
        GROUP  BY image_id) t2 
       JOIN image_data d 
         ON d.image_id = t2.image_id 
ORDER  BY metric DESC 
LIMIT  50 ";

		$q = $this->db->query($sql);
		if($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}

	public function update_quantiles()
	{
		//This is a heavy query. The query results are cached. Execution times are over
		// 10 seconds with 20k images.
		$this->load->driver('cache', array('adapter' => 'file'));

		if( ! $quantiles = $this->cache->get('quantiles'))
		{
			//Try to get the results, hope the user waits long enough.
			$this->calc_quantiles(10);

			//Don't do this recursively, if the query fails there will be a infinite loop
			if( ! $quantiles = $this->cache->get('quantiles'))
			{
				show_error('Could not get query result from cache.');
				return FALSE;
			}
		}
		return $quantiles;
	}
	
	public function get_data($img_id)
	{
		$sql = "SELECT 
					i.id, 
					d.width * d.height / 150000 + i.rating AS rating, 
					uploaded,
					u.name AS username,
					title,
					GROUP_CONCAT(tag) AS tags
				FROM image i
				JOIN image_data d 
					ON i.id = d.image_id
				JOIN user_image ui
					ON ui.image_id = i.id
				JOIN user u
					ON ui.user_id = u.id
				JOIN user_data ud
					ON ud.user_id = u.id
				LEFT JOIN image_tag_map itm
					ON i.id = itm.image_id
				LEFT JOIN tag t
					ON t.id = itm.tag_id
				WHERE i.id = ?
				GROUP BY i.id";
		$q = $this->db->query($sql, $img_id);
		if($q->num_rows() == 1)
		{
			return $q->row_array();
		}
		return FALSE;
	}

	public function random()
	{
		$sql = "SELECT id FROM image";
		$q = $this->db->query($sql);
		$rows = $q->result_array();
		if(count($rows) < 10)
		{
			$this->session->set_flashdata('error', 'Not enough images to get a random image. Please upload some extra images.');
			redirect('/upload');
		}
		$this->load->helper('array');
		$rand_row = random_element($rows);

		return $rand_row['id'];
	}

	public function from_queue()
	{
		$this->load->model('queue_model', 'queue');

		if( ! $img_id = $this->queue->get())
		{
			//Queue is empty, return a random image
			return $this->random();
		}
		return $img_id;
	}

	public function get_rating($id)
	{
		$sql = "
			SELECT d.width * d.height / 150000 + i.rating AS rating 
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
			WHERE i.id = ?";
		$q = $this->db->query($sql, $id);

		if($q->num_rows() == 1)
		{
			$row = $q->row();
			return $row->rating;
		}
		return FALSE;
	}

	public function calc_quantiles($nth)
	{

		//Copyrighted Roland Bouman
		//http://forge.mysql.com/tools/tool.php?id=149
		$this->db->query("SET @quantiles:= ?", $nth);
		$sql = "
			SELECT	rating                     AS metric,
					@n DIV (@c DIV @quantiles) AS quantile,
					@n                         AS N
			FROM	image
			CROSS JOIN (
						SELECT @n:=0,
						@c:=COUNT(*)
			            FROM	image
			           ) c
			WHERE      NOT (
			               (@n:=@n+1) % (@c DIV @quantiles)
			           ) 
			ORDER BY   rating";
		$q = $this->db->query($sql);
		$this->load->driver('cache', array('adapter' => 'file'));

		if($q->num_rows() > 0)
		{
			$ttl = 3600 + 100;
			//Longer than a hour so the results are always from the cache
			$this->cache->save('quantiles', $q->result_array(), $ttl);
			return TRUE;
		}

		log_message('error', 'Failed to get the quantiles from the database.');
		//If there arn't enough images there won't be any result. To prevent the login
		// from failing set some bogus data.
		$this->cache->save('quantiles', array( array('metric' => 1200, 'quantile' => 1, 'N' => 1)), 100);
		return FALSE;
	}
	
	public function add_tag($img_id, $tag, $user_id)
	{
		$this->load->model('users_model', 'user');
		$this->config->load('karma');
		$this->config->load('points');
		
		//Leave only letter, numbers and spaces
		$tag = trim(strtolower(preg_replace('[^\w\s]', ' ', $tag)));
		
		$sql = 'SELECT id FROM tag WHERE tag = ?';
		$query = $this->db->query($sql, $tag);
		
		if($query->num_rows() == 1)
		{
			$row = $query->row_array(); 
			$tag_id = $row['id'];
		}
		else
		{
			if( ! $this->auth->is_autoconfirmed())
			{
				//When a user is not autoconfirmed he is considerd as new to the site. He could add 'wrong' tags
				return;
			}
			$sql = 'INSERT INTO tag (tag) VALUES (?)';
			$query = $this->db->query($sql, $tag);
			$tag_id = $this->db->insert_id();
			$this->user->add_karma($this->session->userdata('user_id'), $this->config->item('tag_create_karma'));
		}
		
		$sql = 'INSERT IGNORE INTO image_tag_map (image_id, tag_id, user_id) VALUES (?, ?, ?)';
		$this->db->query($sql, array($img_id, $tag_id, $user_id));
		
		$this->user->add_karma($this->session->userdata('user_id'), $this->config->item('tag_karma'));
		
		$this->config->load('points');
		$this->add_points($img_id, $this->config->item('tag_points'));
	}

	public function add_points($img_id, $points)
	{
		$data = array(
			'image_id' => $img_id,
			'points' => $points,
			'given' => date('Y-m-d')
		);
		
		if($this->db->insert('image_point', $data))
		{
			return TRUE;
		}
		return FALSE;
	}
}