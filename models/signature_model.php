<?php
class Signature_model extends CI_Model {
	
	private function split_into_words($sig) {
	    $words = array();

		for ($i=0; $i < $this->config->item('max_words'); $i++) { 
			$words[] = substr($sig, $i, $this->config->item('max_word_length'));
		}

	    return $words;
	}
	
	public function save($md5, $cvec) {
	    $compressed_cvec = puzzle_compress_cvec($cvec);
	    $words = $this->split_into_words($cvec);
		
		$sql = "SELECT id FROM image WHERE digest = ?"; 
		$r = $this->db->query($sql, array($md5));
		if ($r->num_rows() > 0) {
			$row = $r->row(); 
			
			//It is a exact duplicate based on the md5
			return $row->id;
		}
		
		//No duplicate found
		$sql = "INSERT INTO image (digest, uploaded) VALUES (?, ?)";
		$this->db->query($sql, array($md5, date("Y-m-d H:i:s")));
		$image_id = $this->db->insert_id();
		
		//Insert the new signature in the db
		$sql = "INSERT INTO signature (compressed_signature, image_id) VALUES (?, ?)";
		$this->db->query($sql, array($compressed_cvec, $image_id));
		$signature_id = $this->db->insert_id();
		
		//Insert all the words
		foreach ($words as $u => $word) {
			$data[] = array('pos_and_word' => chr($u) . puzzle_compress_cvec($word), 'signature_id' => $signature_id);
		}
		$this->db->insert_batch('word', $data);

		return $image_id;
	}

	public function similar($md5, $cvec, $threshold = PUZZLE_CVEC_SIMILARITY_THRESHOLD) {

	    $words = $this->split_into_words($cvec);
		$sql = 'SELECT compressed_signature, image_id 
				FROM signature
				JOIN word 
					ON word.signature.id = signature.id
				WHERE pos_and_word 
					IN (' . $this->implode_words($words) . ')';
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			$scores = array();
			
		    foreach ($query->result() as $row) {
				$uncompressed_cvec = puzzle_uncompress_cvec($row->compressed_signature);
				$distance = puzzle_vector_normalized_distance($cvec, $uncompressed_cvec);
				
				if ($distance < $threshold && $distance > 0.0) {
					$scores[$picture_id] = $distance;
				}
			}
		}
	    return $scores;
	}

	private function implode_words($words) {
		$glue = FALSE;
	    foreach ($words as $u => $word) {
			if ($glue === TRUE) {
			    $str .= ',';
			}
			
			$str .= chr($u) . puzzle_compress_cvec($word);
			$coma = TRUE;
	    }
		return $str;
	}
}
