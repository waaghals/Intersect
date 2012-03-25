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