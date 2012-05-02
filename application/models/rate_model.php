<?php

class Rate_model extends CI_Model {

	
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function update_winner($winner_id)
	{
		return $this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->elo->get_winner_rating(), $winner_id));

	}

	public function update_loser($loser_id)
	{
		return $this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->elo->get_loser_rating(), $loser_id));
	}
	
	public function add_user_rate($winner, $loser)
	{
		return $this->db->query('INSERT INTO user_rate (user_id, win_id, los_id, date) VALUES (?, ?, ?, CURDATE())', array($this->session->userdata('user_id'), $winner, $loser));
	}
	
	public function add_rating($img_id, $amount)
	{
		return $this->db->query('INSERT INTO image_rate (image_id, amount) VALUES (?, ?)', array($img_id, $amount));
	}
	
	public function get_rating($img_id)
	{
		$query = $this->db->query('SELECT SUM(amount) AS amount FROM image_rate WHERE image_id = ?', $img_id);
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array(); 
			return $row['amount'] + 1200;
		}
		return FALSE;
	}
}