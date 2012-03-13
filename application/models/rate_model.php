<?php
/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Priyesh Patel <priyesh@pexat.com>
 * @copyright Copyright (c) 2011 onwards, Priyesh Patel
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License
 */
class Rating_model extends CI_Model {

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

	 /**
     * Costructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */
	public function  __construct()
    {
    	// Call the Model constructor
        parent::__construct();
    }
	
	public function set_winner($rating) {
        $this->ratingA = $rating;
        $this->scoreA = 1;
	}
	
	public function set_loser($rating) {
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
	public function calc_new_scores() {
		$expected_scores = $this->get_expected_scores($this->ratingA, $this->ratingB);
        $this->expectedA = $expected_scores['a'];
        $this->expectedB = $expected_scores['b'];
		
		$new_ratings = $this->get_new_ratings($this->ratingA, $this->ratingB, $this->expectedA, $this->expectedB, $this->scoreA, $this->scoreB);
        $this->newRatingA = $newRatings['a'];
        $this->newRatingB = $newRatings['b'];
	}
	
    protected function get_expected_scores($ratingA,$ratingB)
    {
        $expectedScoreA = 1 / ( 1 + ( pow( 10 , ( $ratingB - $ratingA ) / 400 ) ) );
        $expectedScoreB = 1 / ( 1 + ( pow( 10 , ( $ratingA - $ratingB ) / 400 ) ) );

        return array (
            'a' => $expectedScoreA,
            'b' => $expectedScoreB
        );
    }

    protected function get_new_ratings($ratingA,$ratingB,$expectedA,$expectedB,$scoreA,$scoreB)
    {
        $newRatingA = $ratingA + ( self::KFACTOR * ( $scoreA - $expectedA ) );
        $newRatingB = $ratingB + ( self::KFACTOR * ( $scoreB - $expectedB ) );

        return array (
            'a' => $newRatingA,
            'b' => $newRatingB
        );
    }
}
