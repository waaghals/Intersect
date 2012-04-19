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
		$sql = "SELECT slug, title, markdown FROM page AS p JOIN revisions AS r ON (p.latest_rev_id = r.id) WHERE slug = ?";
		$query = $this->db->query($sql, $slug);
		if($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		return FALSE;
	}
	
	function add($slug, $title, $content)
    {
    	$rev_id = $this->create_revision($content, 'Page created');
		
    	$sql = "INSERT IGNORE INTO page (slug, title, latest_rev_id) VALUES (?, ?, ?)";
        $query = $this->db->query($sql, array($slug, $title, $rev_id));
		if($this->db->affected_rows() > 0)
		{
			return TRUE;
		}
		return FALSE;
    }
	
	function modify($slug, $content, $comment)
    {
    	$rev_id = $this->create_revision($content, 'Page created');
		
		$sql = "UPDATE IGNORE page SET latest_rev_id = ? WHERE slug = ?";
        $query = $this->db->query($sql, array($rev_id, $slug));
    	if($this->db->affected_rows() == 1)
		{
			return TRUE;
		}
		return FALSE;
    }

	private function create_revision($content, $comment)
	{
		$user_id = $this->session->userdata('user_id');
    	$sql = "INSERT INTO revisions (user_id, markdown, modified, comment) VALUES (?, ?, ?, ?)";
		$query = $this->db->query($sql, array($user_id, $content, date("Y-m-d H:i:s"), $comment));
		if($this->db->affected_rows() == 1)
		{
			return $this->db->insert_id();
		}
		show_error('Could not create revision');
	}
}

/* End of file page_model.php */
/* Location: ./application/models/page_model.php */