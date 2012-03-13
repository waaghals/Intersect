<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rate extends CI_Controller {

	public function index()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('winner', 'Winner', 'required|numeric');
		$this->form_validation->set_rules('loser', 'Loser', 'required|numeric');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('myform');
			
		}
		else
		{
			//$this->load->view('formsuccess');
			
			//Winner Rating
			$sql = "
				SELECT d.width * d.height / 150000 + i.rating AS rating 
				FROM image AS i
					JOIN image_data AS d 
						ON ( i.id = d.image_id ) 
				WHERE i.id = ? 
				LIMIT 0,1";
			$r = $this->db->query($sql, array($this->input->post('winner')));
			$row = $r->row();
			$winner_rating = $row->rating;
			
			//Loser Rating
			$sql = "
				SELECT d.width * d.height / 150000 + i.rating AS rating 
				FROM image AS i
					JOIN image_data AS d 
						ON ( i.id = d.image_id ) 
				WHERE i.id = ? 
				LIMIT 0,1";
			$r = $this->db->query($sql, array($this->input->post('loser')));
			$row = $r->row();
			$loser_rating = $row->rating;
			
			if( ! $winner_rating || ! $loser_rating)
			{
				//Id's are unvalid
				show_error('Image does not exist');
			}

			$this->load->model('Rate_model', 'rate');
			$this->rate->set_winner($winner_rating);
			$this->rate->set_loser($loser_rating);
			$this->rate->calc_new_ratings();
			
			//Update the new ratings
			$q = $dbh->prepare('UPDATE image SET rating = ? WHERE id = ?');
			$q->execute(array($this->rate->get_winner_rating(), $this->input->post('winner')));
		
			$q = $dbh->prepare('UPDATE image SET rating = ? WHERE id = ?');
			$q->execute(array($this->rate->get_loser_rating(), $this->input->post('loser')));
			
		}
	}
}

/* End of file rate.php */
/* Location: ./application/controllers/rate.php */