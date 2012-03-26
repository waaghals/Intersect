<?php
class Images_model extends CI_Model {
	
	public function best($number) {
		return $this->images($number, FALSE);
	}
	
	public function worst($numer) {
		return $this->images($number, TRUE);
	}
	
	private function images($number, $worst) {
		//Order asc when worst, desc when best
		$order = ($worst) ? 'desc': 'asc';
			
		$sql = "
			SELECT 
				i.id, 
				CONCAT( replace( CONVERT( datetime, date ) , '-', '/' ) , '/', digest, '.', extension ) AS path, 
				d.width * d.height / 150000 + i.rating AS rating
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
			ORDER BY score ?
			LIMIT 0 , ?";
			
		$q = $this->db->query($sql);	
		if ($q->num_rows() > 0)
		{
			return $q->result();
		}
		return FALSE;
	}
	
	
	public function percentile() {
		
		//This is a heavy query. The query results are cached with cron with the cron_model. Execution times are over 10 seconds with 20k images.
		$this->load->driver('cache');

		if ( ! $percentile_result = $this->cache->get('percentile_result')) {
			show_error('Could not get query result from cache.');
			return FALSE;
 		}
		return $percentile_result;
	}
	
	public function random() {
		
		$sql = "SELECT 
					MAX(`id`) AS max_id , 
					MIN(`id`) AS min_id 
				FROM image";
		$q = $this->db->query($sql, array($md5, $extension, date("Y-m-d H:i:s")));
		$row = $q->row();
		
		$sql = "SELECT 
					id, 
					digest, 
					extension, 
					replace(CONVERT(datetime,date), \'-\', \'/\') AS datetime 
				FROM image 
				WHERE id >= ?";
		$q = $this->db->query($sql, array(mt_rand($row->min_id, $row->max_id)));
		$row = $q->row();
		
		$path = $row->datetime . '/' . $row->digest . '.' . $row->extension;
		return array('path' => $path, 'id' => $row->id);
	}

	public function get_rating($id) {
		$sql = "
			SELECT d.width * d.height / 150000 + i.rating AS rating 
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
			WHERE i.id = ?";
		$q = $this->db->query($sql, $id);
		
		if ($query->num_rows() == 1) {
			$row = $q->row();
			return $row->rating;
		}
		return FALSE;
	}
	
	public function get_image($id) {
		$sql = "SELECT 
					id, 
					digest, 
					extension, 
					replace(CONVERT(datetime,date), \'-\', \'/\') AS datetime 
				FROM image 
				WHERE id = ?";
		$q = $this->db->query($sql, $id);
		$row = $q->row();
		
		$path = $row->datetime . '/' . $row->digest . '.' . $row->extension;
		return array('path' => $path, 'id' => $row->id);
	}
}