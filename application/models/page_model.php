<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Page_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}

	function load($slug)
	{
		$sql = "SELECT slug, title, content FROM page WHERE slug = ?";
		$query = $this->db->query($sql, $slug);
		if($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		return FALSE;
	}
}

/* End of file page_model.php */
/* Location: ./application/models/page_model.php */