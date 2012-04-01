<?php
class Images_model extends CI_Model {
	
	function __construct() {
		// Call the Model constructor
		parent::__construct();
		$this->load->helper('path');
	}
	public function best($number) {
		return $this->images($number, FALSE);
	}
	
	public function worst($numer) {
		return $this->images($number, TRUE);
	}
	
	private function images($number, $worst) {
		if( ! is_numeric($number)) {
			show_error('Number of images is not a valid number.');
			return;
		}
		//Order asc when worst, desc when best
		$order = ($worst) ? 'ASC': 'DESC';
			
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
		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}
		return FALSE;
	}
	
	
	public function percentile() {
		//This is a heavy query. The query results are cached. Execution times are over 10 seconds with 20k images.
		$this->load->driver('cache', array('adapter'=>'file'));    

		if ( ! $percentile_result = $this->cache->get('percentile_result')) {
			//Try to get the results, hope the user waits long enough.
			$this->update_percentile();
			
			//Don't do this recursively, if the percentile query fails there will be a infinite loop
			if ( ! $percentile_result = $this->cache->get('percentile_result')) {
				show_error('Could not get query result from cache.');
				return FALSE;
			}
 		}
		return $percentile_result;
	}
	
	public function random() {
		$sql = "SELECT id FROM image";
		$q = $this->db->query($sql);
		$rows = $q->result_array();
		
		$this->load->helper('array');
		$rand_row = random_element($rows);

		return path_to_image($rand_row['id']) . $rand_row['id'];
	}

	public function get_rating($id) {
		$sql = "
			SELECT d.width * d.height / 150000 + i.rating AS rating 
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
			WHERE i.id = ?";
		$q = $this->db->query($sql, $id);
		
		if ($q->num_rows() == 1) {
			$row = $q->row();
			return $row->rating;
		}
		return FALSE;
	}
	
	public function update_percentile() {
		
		$sql = "SELECT 	g2.id AS id,
				       	SUM(g1.r) /
				  (SELECT COUNT(*)
				   FROM image) AS percentile,
				   		d.width AS width,
				   		d.height AS height,
				  		ROUND(250 / d.height * d.width) AS twidth,
				   		250 AS theight,
				   		NULL AS vwidth
				FROM
				  ( SELECT COUNT(*) r,
				           rating
				   FROM image
				   GROUP BY rating )g1
				JOIN
				  ( SELECT COUNT(*) r,
				           rating,
				           digest,
				           id
				   FROM image
				   GROUP BY rating )g2 ON g1.rating < g2.rating
				JOIN image_data as d ON g2.id = d.image_id
				GROUP BY g2.rating HAVING percentile >= 0.99";
		$q = $this->db->query($sql);	
		$this->load->driver('cache', array('adapter'=>'file'));    
		
		if ($q->num_rows() > 0) {
			$ttl = 3600 + 100; //Longer than a hour so the results are always from the cache
			$this->cache->save('percentile_result', $q->result_array(), $ttl);
			return TRUE;
		}
		
		log_message('error', 'Failed to get the 99 percentile from the database.');
		show_error('Failed to get the 99 percentile from the database.');
		$this->cache->save('percentile_result', FALSE, $ttl);
		return FALSE;
	}
}