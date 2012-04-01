<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function hourly() {
		$this->load->model('tag_model', 'tag');
		echo "Updating tag graph: ";
		if ($this->tag->update_graph()) {
			echo "Success\n";
		} else {
			echo "Failed \n";
		}
		
		$this->load->model('images_model', 'images');
		echo "Updating percentile: ";
		if ($this->images->update_percentile()) {
			echo "Success\n";
		} else {
			echo "Failed \n";
		}
	}
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */