<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('cron_model', 'cron');
	}
	
	public function hourly() {
		$this->cron->update_tag_map();
		$this->cron->update_percentile();
	}
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */