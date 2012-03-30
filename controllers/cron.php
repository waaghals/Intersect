<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('cron_model', 'cron');
	}
	
	public function hourly() {
		echo "Updating tag graph: ";
		if ($this->cron->update_tag_graph()) {
			echo "Success\n";
		} else {
			echo "Failed \n";
		}
		
		echo "Updating percentile: ";
		if ($this->cron->update_percentile()) {
			echo "Success\n";
		} else {
			echo "Failed \n";
		}
	}
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */