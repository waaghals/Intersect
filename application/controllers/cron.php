<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function one()
	{
		$this->load->model('users_model', 'users');
		echo "Updating user data table: ";
		if($this->users->update_user_data_table())
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}
	}

	public function five()
	{
		$this->load->driver('cache', array('adapter' => 'file'));
		echo "Cleaning cache: ";
		if($this->cache->clean())
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}
	}

	public function hourly()
	{
		$this->load->model('tag_model', 'tag');
		echo "Updating tag graph: ";
		if($this->tag->update_graph())
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}

		$this->load->model('images_model', 'images');
		echo "Updating quantiles: ";
		if($this->images->update_quantiles())
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}

		$this->load->model('queue_model', 'queue');
		echo "Purging queue: ";
		if($this->queue->purge())
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}
	}

	public function daily()
	{
		$this->load->driver('cache', array('adapter' => 'file'));
		$this->load->helper('directory');

		//Set a cache file, this will take 1 minutes at most.
		$this->cache->save('vacuuming', TRUE, 60);
		echo "Cleaning upload folder: ";
		//First wait 10 seconds so that any image that ar being uploaded don't get
		// interupted
		sleep(10);

		//Remove the files
		$map = directory_map(APPPATH . 'tmp/', 1);
		var_dump($map);
		foreach($map as $location)
		{
			$location = APPPATH . 'tmp/' . $location;
			if(is_file($location))
			{
				unlink($location);
			}
		}
		if($this->cache->delete('vacuuming'))
		{
			echo "Success\n";
		}
		else
		{
			echo "Failed \n";
		}
	}

}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */