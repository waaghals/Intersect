<?php
class Queue_model extends CI_Model {
	
	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}
	
	public function purge() {
		if( ! $this->db->query("DELETE FROM image_queue WHERE state = 'C'")) {
			return FALSE;
		}
		
		if( ! $this->db->query("UPDATE image_queue SET state = 'O' , modified = NOW() WHERE state = 'A' AND DATEDIFF(NOW(), modified) > 1")) {
			return FALSE;
		}
	}
	
	public function add($image_id, $state = 'O') {
		$this->db->query("INSERT INTO image_queue(image_id, state, modified) VALUES (?, ?, NOW())", array($image_id, $state));
	}
	
	public function modify($image_id, $state = 'C') {
		$this->db->query("UPDATE IGNORE image_queue SET state = ? , modified = NOW() WHERE image_id = ?", array($state, $image_id));
	}
	
	public function get($n = 1) {
		
		//Don't get the users own images from the queue.
		//This prevents the user imediatly seeing his own image on the rating page.
		$sql = "SELECT iq.image_id 
					FROM image_queue AS iq 
					JOIN user_image AS ui 
						ON (ui.image_id = iq.image_id) 
					WHERE state = 'O' 
						AND user_id <> ? 
					ORDER BY modified ASC 
					LIMIT ?";
		$query = $this->db->query($sql, array($this->auth->get_user_id(), $n));
		
		if ($query->num_rows() > 1) {
			foreach($query->result_array() as $row) {
				$this->modify($row['image_id'], 'A');
			}
			return $query->result_array();
		}
		elseif ($query->num_rows() == 1) {
			$row = $query->row_array();
			
			$this->modify($row['image_id'], 'A');
			return $row['image_id'];
		}
		else {
			return FALSE;
		}
	}
}