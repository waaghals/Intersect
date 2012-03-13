<?php
class Signature_model extends CI_Model {
	
	function split_into_words($sig) {
	    $words = array();
	    $u = 0;    
	    do {
			$words[$u] = substr($sig, $u, $this->config->item('max_words_length'));
	    } while (++$u < $this->config->item('max_words'));
	
	    return $words;
	}
	
	function save_signature($md5, $cvec, $extension) {
	    $compressed_cvec = puzzle_compress_cvec($cvec);
	    $words = $this->split_into_words($cvec);
		
		$this->db->trans_start();
		$sql = "SELECT id FROM image WHERE digest = ?"; 
		$r = $this->db->query($sql, array($md5));
		if ($r->num_rows() > 0) {
			$row = $r->row(); 
			$image_id = $row->id;
			
			$this->db->trans_complete();
			return TRUE;
		}
		
		//No duplicate found
		$sql = "INSERT INTO image (digest, extension, datetime) VALUES (?, ?, ?)";
		$this->db->query($sql, array($md5, $extension, date("Y-m-d H:i:s")));
		$image_id = $this->db->insert_id();
		
		//Insert the new signature in the db
		$sql = "INSERT INTO signature (compressed_signature, image_id) VALUES (?, ?)";
		$this->db->query($sql, array($compressed_cvec, $image_id));
		$signature_id = $this->db->insert_id();
		
		//Insert all the words
		$sql = "INSERT INTO word (pos_and_word, signature_id) VALUES (:pos_and_word, :signature_id)";
		foreach ($words as $u => $word) {
			$this->db->query($sql, array(chr($u) . puzzle_compress_cvec($word), $signature_id));
		}
		$this->db->trans_complete();
	}
}
