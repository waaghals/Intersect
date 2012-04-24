<?php
class Tag_model extends CI_Model {

	public function for_image($id)
	{
		$sql = "SELECT 
					GROUP_CONCAT(tag) AS tags
				FROM tag
				JOIN image_tag_map ON image_tag_map.tag_id = tag.id
				WHERE image_id = ?
				GROUP BY image_id";
		$q = $this->db->query($sql, $id);

		if($q->num_rows() == 1)
		{
			$row = $q->row_array(); 
			return $row['tags'];
		}
		return FALSE;
	}

	public function update_graph()
	{
		$this->db->trans_start();
		$this->db->query('TRUNCATE TABLE tag_graph');
		$this->db->query('INSERT INTO tag_graph (origid, destid, weight)
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
		if($this->db->trans_status() === FALSE)
		{
			log_message('error', 'Could not update the tag_graph table');
			show_error('Could not update the tag_graph table');
			return FALSE;
		}
		return TRUE;
	}

}