<?php
class Signature_model extends CI_Model {
	
	private function split_into_words($sig) {
	    $words = array();
		for ($i=0; $i < $this->config->item('max_words'); $i++) { 
			$words[] = substr($sig, $i, $this->config->item('max_word_length'));
		}

	    return $words;
	}
	
	public function save($hash, $cvec) {
	    $words = $this->split_into_words($cvec);
		$sql = "SELECT id FROM image WHERE LOWER(HEX(hash)) = ?"; 
		$r = $this->db->query($sql, $hash);
		if ($r->num_rows() > 0) {
			$row = $r->row(); 
			
			//It is a exact duplicate based on the hash
			return $row->id;
		}
		
		//No duplicate found
		$this->db->set('hash', 'UNHEX(\'' . $hash . '\')', FALSE);
		$this->db->set('uploaded', date("Y-m-d H:i:s"));
		$this->db->insert('image');
		$image_id = $this->db->insert_id();
		
		//Insert the new signature in the db
		$sql = "INSERT INTO signature (compressed_signature, image_id) VALUES (?, ?)";
		$this->db->query($sql, array($cvec, $image_id));
		$signature_id = $this->db->insert_id();
		
		//Insert all the words
		foreach ($words as $u => $word) {
			$data[] = array('pos_and_word' => chr($u) . $word, 'signature_id' => $signature_id);
		}
		$this->db->insert_batch('word', $data);

		return $image_id;
	}

	public function similar($cvec, $threshold = PUZZLE_CVEC_SIMILARITY_THRESHOLD) {
	    $words = $this->split_into_words2($cvec);
		$sql = 'SELECT signature, image_id 
				FROM signature
				JOIN word 
					ON word.signature.id = signature.id
				WHERE pos_and_word
					IN (' . $this->implode_words($words) . ')';
		//$query = $this->db->query($sql);
		
	$dbh = new PDO('mysql:host=localhost;dbname=woot_new', 'root', 'sbA9f909nEpmExOy');
    $dbh->beginTransaction();
    $sql2 = 'SELECT compressed_signature, image_id 
				FROM signature
				JOIN word 
					ON word.signature_id = signature.id
				WHERE pos_and_word
					IN (';
    $coma = FALSE;
    foreach ($words as $u => $word) {
		if ($coma === TRUE) {
		    $sql2 .= ',';
		}
		
		$sql2 .= $dbh->quote(chr($u) . puzzle_compress_cvec($word));
		$coma = TRUE;
    }
    $sql2 .= ')';
	
	var_dump($sql, $sql2); exit;	
		//$query = $this->db->query($sql);
 
		var_dump($dbh->query($sql2)); 
		exit;

		if ($query->num_rows() > 0) {
			$scores = array();
			
		    foreach ($query->result() as $row) {
				$distance = puzzle_vector_normalized_distance($cvec, $row->signature);
				
				if ($distance < $threshold && $distance > 0.0) {
					$scores[$picture_id] = $distance;
				}
			}
		}
	    return $scores;
	}

	private function implode_words($words) {
		$glue = FALSE;
		$str = NULL;
	    foreach ($words as $u => $word) {
			if ($glue === TRUE) {
			    $str .= ', ';
			}
			
			$str .= $this->db->escape(chr($u) . $word);
			$glue = TRUE;
	    }
		return $str;
	}
	
	public function get_signature($img_id) {
		$sql = "SELECT compressed_signature FROM signature WHERE image_id = ?";
		$query = $this->db->query($sql, $img_id);
		
		if ($query->num_rows() == 1) {
			$row = $query->row_array();
			return $row['compressed_signature'];
		}
		return FALSE;
	}
	
	function similar_new($cvec, $threshold = PUZZLE_CVEC_SIMILARITY_THRESHOLD) {

	    $uncompressed_cvec = puzzle_uncompress_cvec($cvec);
	    $words = $this->split_into_words($uncompressed_cvec);
		
	    $dbh = new PDO('mysql:host=localhost;dbname=woot', 'root', 'sbA9f909nEpmExOy');
	    $dbh->beginTransaction();
	    $sql = 'SELECT DISTINCT image_id, compressed_signature
				FROM signature
				RIGHT JOIN word 
					ON word.signature_id = signature.id
				WHERE pos_and_word
					IN (';
	    $coma = FALSE;
	    foreach ($words as $u => $word) {
			if ($coma === TRUE) {
			    $sql .= ',';
			}
			
			$sql .= $dbh->quote(chr($u) . puzzle_compress_cvec($word));
			$coma = TRUE;
	    }
	    $sql .= ')';
		
	    $res_words = $dbh->query($sql);

		$scores = array();   
	    while (($row = $res_words->fetch()) !== FALSE) {
			$found_cvec = puzzle_uncompress_cvec($row['compressed_signature']);
			$distance = puzzle_vector_normalized_distance($uncompressed_cvec, $found_cvec);
			
			if ($distance < $threshold && $distance > 0.0) {
			    $scores[$row['image_id']] = $distance;
			}
		}
		var_dump($scores); exit;
	    return $scores;
	}
	
	function find_similar_pictures($cvec, $is_cvec_comp = FALSE,
		$threshold = PUZZLE_CVEC_SIMILARITY_THRESHOLD) {
		
		if($is_cvec_comp)
		{
			$cvec = puzzle_uncompress_cvec($cvec);
		}
		
	    $compressed_cvec = puzzle_compress_cvec($cvec);
	    $words = $this->split_into_words($cvec);
		
	    $dbh = new PDO('mysql:host=localhost;dbname=woot', 'root', 'sbA9f909nEpmExOy');
	    $dbh->beginTransaction();
	    $sql = 'SELECT * 
				FROM signature
				RIGHT JOIN word 
					ON word.signature_id = signature.id
				WHERE pos_and_word
					IN (';
	    $coma = FALSE;
	    foreach ($words as $u => $word) {
			if ($coma === TRUE) {
			    $sql .= ',';
			}
			
			$sql .= $dbh->quote(chr($u) . puzzle_compress_cvec($word));
			$coma = TRUE;
	    }
	    $sql .= ')';
		
		var_dump($sql);
	    $res_words = $dbh->query($sql);

		$scores = array();   
	    while (($row = $res_words->fetch()) !== FALSE) {
			$found_cvec = puzzle_uncompress_cvec($row['compressed_signature']);
			$distance = puzzle_vector_normalized_distance($cvec, $found_cvec);
			if ($distance < $threshold && $distance > 0.0) {
			    $scores[$row['image_id']] = $distance;
			}
		}
	    return $scores;
	}

	function get_image_info($image_id) {
		$dbh = new PDO('mysql:host=localhost;dbname=woot', 'root', 'sbA9f909nEpmExOy');
		$q = $dbh->prepare("SELECT s.compressed_signature AS cvec, d.width * d.height /150000 + i.rating AS score, d.width, d.height, d.lat, d.lng
		FROM image AS i
		JOIN image_data AS d ON ( i.id = d.image_id ) 
		JOIN signature AS s ON ( i.id = s.image_id ) 
		WHERE i.id = :id");
		$q->execute(array(':id' => $image_id));
		$row = $q->fetch();
		if(is_array($row)) {
			return $row;
		}
		else {
			return FALSE;
		}
	}
}
