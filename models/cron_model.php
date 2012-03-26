<?php
class Cron_model extends CI_Model {
	
	public function update_tag_graph($number) {
		$this->db->trans_start();
		$this->db->query('TRUNCATE TABLE tag_graph');
		$this->db->query('INSERT INTO tag_graph (origid,destid, weight)
							SELECT	a.tag_id AS origid,
									b.tag_id AS destid,
									COUNT(DISTINCT a.image_id) /
								(SELECT COUNT(DISTINCT id)
								FROM image AS i
								JOIN image_tag_map AS tm 
									ON i.id = tm.image_id) 
									AS weight
							FROM image_tag_map a
							JOIN image_tag_map b 
								ON a.tag_id != b.tag_id
									AND a.image_id = b.image_id
							GROUP BY a.tag_id,
									b.tag_id
							ORDER BY weight DESC');
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
     		log_message('error', 'Could not update the tag_graph table');
		}
	}
	
	public function update_percentile() {
		
		$sql = "SELECT g2.id AS id,
				       SUM(g1.r) /
				  (SELECT COUNT(*)
				   FROM image) AS percentile,
				                   CONCAT(replace(CONVERT(g2.datetime, date) , '-', '/') , '/', g2.digest, '.', g2.extension) AS path
				FROM
				  ( SELECT COUNT(*) r,
				           rating
				   FROM image
				   GROUP BY rating )g1
				JOIN
				  ( SELECT COUNT(*) r,
				           rating,
				           datetime,
				           digest,
				           extension,
				           id
				   FROM image
				   GROUP BY rating )g2 ON g1.rating < g2.rating
				GROUP BY g2.rating HAVING percentile >= 0.99";
		$q = $this->db->query($sql);	
		$this->load->driver('cache');
		
		if ($q->num_rows() > 0) {
			$ttl = 3600 + 100; //Longer than a hour so the results are always from the cache
			$this->cache->save('percentile_result', $q->result(), $ttl);
			return TRUE;
		}
		
		log_message('error', 'Failed to get the 99 percentile from the database.');
		$this->cache->save('percentile_result', FALSE, $ttl);
		return FALSE;
	}

}