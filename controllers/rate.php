<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rate extends CI_Controller {


	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		
		if( ! $this->auth->is_allowed()) {
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/upload');
		}
	}
	
	public function index()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('winner', 'Winner', 'required|numeric');
		$this->form_validation->set_rules('loser', 'Loser', 'required|numeric');

		if ($this->form_validation->run() == FALSE)
		{
			show_error('You messed up!');
		}
		else
		{
			$winner = $this->input->post('winner');
			$loser = $this->input->post('loser');
			
			$this->load->model('Images_model', 'images');
			$winner_rating = $this->images->get_rating($winner);
			$loser_rating = $this->images->get_rating($loser);
			
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
			$this->db->trans_start();
			$this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->rate->get_winner_rating(), $winner));
			$this->db->query('UPDATE image SET rating = ? WHERE id = ?', array($this->rate->get_loser_rating(), $loser));
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				show_error('Error on updating the image ratings');
			}
			
			$this->load->helper('url');
			redirect('/');
		}
	}
}

/* End of file rate.php */
/* Location: ./application/controllers/rate.php */