<?php

class Rate_model extends CI_Model {

	
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function update_winner($winner_id)
	{
		$this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->elo->get_winner_rating(), $winner_id));

	}

	public function update_loser($loser_id)
	{
		$this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->elo->get_loser_rating(), $loser_id));
	}
	
	public function add_user_rate($winner, $loser)
	{
		$this->db->query('INSERT INTO user_rate (user_id, win_id, los_id, date) VALUES (?, ?, ?, CURDATE())', array($this->session->userdata('user_id'), $winner, $loser));
	}
}