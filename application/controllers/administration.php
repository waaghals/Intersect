<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Administration extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if( ! $this->auth->is_admin())
		{
			show_404();
		}
	}

	public function migrate()
	{
		$this->load->library('migration');
		$this->output->enable_profiler(TRUE);

		if( ! $this->migration->current())
		{
			show_error($this->migration->error_string());
		}
		echo 'Database migration succesfull';
	}

}

/* End of file administration.php */
/* Location: ./application/controllers/administration.php */