<?php
class Image_model extends CI_Model {
	
	function random_image($sig) {
		
		$sql = "SELECT MAX(`id`) AS max_id , MIN(`id`) AS min_id FROM image";
		$r = $this->db->query($sql, array($md5, $extension, date("Y-m-d H:i:s")));
		$row = $r->row();
		
		$sql = "SELECT id, digest, extension, replace(CONVERT(datetime,date), \'-\', \'/\') AS datetime FROM image WHERE id >= ? LIMIT 0,1";
		$r = $this->db->query($sql, array(mt_rand($row->min_id, $row->max_id)));
		$row = $r->row();
		
		$path = $row->datetime . '/' . $row->digest . '.' . $row->extension;
		return array('path' => $path, 'id' => $row->id);
	}
	
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
				d.width * d.height /150000 + i.rating AS rating
			FROM image AS i
				JOIN image_data AS d 
					ON ( i.id = d.image_id ) 
			ORDER BY score ?
			LIMIT 0 , ?";
			
		if ($q->num_rows() > 0)
		{
			return $q->result();
		}
		return FALSE;
	}
}