<?php
class Image_model extends CI_Model {
	
	function random() {
		
		$sql = "SELECT MAX(`id`) AS max_id , MIN(`id`) AS min_id FROM image";
		$q = $this->db->query($sql, array($md5, $extension, date("Y-m-d H:i:s")));
		$row = $q->row();
		
		$sql = "SELECT id, digest, extension, replace(CONVERT(datetime,date), \'-\', \'/\') AS datetime FROM image WHERE id >= ? LIMIT 0,1";
		$q = $this->db->query($sql, array(mt_rand($row->min_id, $row->max_id)));
		$row = $q->row();
		
		$path = $row->datetime . '/' . $row->digest . '.' . $row->extension;
		return array('path' => $path, 'id' => $row->id);
	}
}