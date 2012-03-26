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

}