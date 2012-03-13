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
}