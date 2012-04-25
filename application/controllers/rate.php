<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Rate extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}
	}

	public function index()
	{
		//Please wait while loading...................
		$this->load->library('form_validation');
		$this->load->library('elo');
		$this->load->helper('url');
		$this->load->model('queue_model', 'queue');
		$this->load->model('images_model', 'images');
		$this->load->model('users_model', 'users');
		$this->load->model('rate_model', 'rate');
		$this->config->load('karma');
		$this->config->load('points');

		$this->form_validation->set_rules('winner', 'Winner', 'required|numeric');
		$this->form_validation->set_rules('loser', 'Loser', 'required|numeric');

		if($this->form_validation->run())
		{
			$winner = $this->input->post('winner');
			$loser = $this->input->post('loser');

			$winner_rating = $this->images->get_rating($winner);
			$loser_rating = $this->images->get_rating($loser);

			if( ! $winner_rating || ! $loser_rating)
			{
				//Id's are unvalid
				redirect('/');
			}

			$this->elo->set_winner($winner_rating);
			$this->elo->set_loser($loser_rating);
			$this->elo->calc_new_ratings();

			//Update the new ratings
			$this->db->trans_start();

			//Change the images ratings
			$this->rate->update_winner($winner);
			$this->rate->update_loser($winner);

			//Remove images from the queue
			$this->queue->modify($winner, 'C');
			$this->queue->modify($loser, 'C');

			//Add what the user rated to the database
			$this->rate->add_user_rate($winner, $loser);
			
			//Give point to the image for the win/los
			$this->images->add_points($winner, $this->config->item('win_points'));
			$this->images->add_points($loser, $this->config->item('los_points'));

			$this->users->add_karma($this->session->userdata('user_id'), $this->config->item('rate_karma'));

			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE)
			{
				show_error('Error on updating the image ratings');
			}
		}
		//Showing warnings is for losers, just don't tell the user he f*cked up and redirect anyway
		redirect('/');
	}
}

/* End of file rate.php */
/* Location: ./application/controllers/rate.php */