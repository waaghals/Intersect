<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Priyesh Patel <priyesh@pexat.com>
 * @copyright Copyright (c) 2011 onwards, Priyesh Patel
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 * License
 */
class Elo {
	
	/**
	 * @var int The K Factor used.
	 */
	const KFACTOR = 32;

	/**
	 * Protected & private variables.
	 */
	protected $ratingA;
	protected $ratingB;
	protected $scoreA;
	protected $scoreB;
	protected $expectedA;
	protected $expectedB;
	protected $newRatingA;
	protected $newRatingB;

	public function set_winner($rating)
	{
		$this->ratingA = $rating;
		$this->scoreA = 1;
	}

	public function set_loser($rating)
	{
		$this->ratingB = $rating;
		$this->scoreB = 0;
	}

	/**
	 * Retrieve the new rating for the winner.
	 *
	 * @return Integer the new rating of the winner.
	 */
	public function get_winner_rating()
	{
		return $this->newRatingA;
	}

	/**
	 * Retrieve the new rating for the loser.
	 *
	 * @return Integer the new rating of the loser.
	 */
	public function get_loser_rating()
	{
		return $this->newRatingB;
	}

	/**
	 * calculate the new ratings for the winner and the loser.
	 *
	 */
	public function calc_new_ratings()
	{
		$expected_scores = $this->get_expected_scores($this->ratingA, $this->ratingB);
		$this->expectedA = $expected_scores['a'];
		$this->expectedB = $expected_scores['b'];

		$new_ratings = $this->get_new_ratings($this->expectedA, $this->expectedB, $this->scoreA, $this->scoreB);
		$this->newRatingA = $new_ratings['a'];
		$this->newRatingB = $new_ratings['b'];
	}

	protected function get_expected_scores($ratingA, $ratingB)
	{
		$expectedScoreA = 1 / (1 + ( pow(10, ($ratingB - $ratingA) / 400)));
		$expectedScoreB = 1 / (1 + ( pow(10, ($ratingA - $ratingB) / 400)));

		return array('a' => $expectedScoreA, 'b' => $expectedScoreB);
	}

	protected function get_new_ratings($expectedA, $expectedB, $scoreA, $scoreB)
	{
		$newRatingA = self::KFACTOR * ($scoreA - $expectedA);
		$newRatingB = self::KFACTOR * ($scoreB - $expectedB);

		return array('a' => $newRatingA, 'b' => $newRatingB);
	}
}

/* End of file Elo.php */
/* Location: ./application/libraries/Elo.php */