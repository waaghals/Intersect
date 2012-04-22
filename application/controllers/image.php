<?php if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Image extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('path', 'url'));

		if( ! $this->auth->is_logged_in())
		{
			$this->session->set_flashdata('warning', 'You are not logged in.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/user/sign_in');
		}
	}

	public function resize($img_id, $width = 400, $height = 250)
	{
		$this->output->cache(60 * 60 * 24);

		$config['image_library'] = 'gd2';
		$config['source_image'] = path_to_image($img_id) . $img_id;
		$config['maintain_ratio'] = TRUE;
		$config['dynamic_output'] = TRUE;
		$config['master_dim'] = 'auto';

		//width and height need to be specified or the image gets skewed
		if(is_numeric($width))
		{
			$config['width'] = $width;
		}
		else
		{
			$config['width'] = $this->config->item('max_width');
		}

		if(is_numeric($height))
		{
			$config['height'] = $height;
		}
		else
		{
			$config['height'] = $this->config->item('max_height');
		}

		$this->load->library('image_lib', $config);
		ob_start();
		$this->image_lib->resize();
		$data['echo_this'] = ob_get_contents();
		ob_end_clean();
		$this->load->view('echo', $data);
	}

	public function fav()
	{
		$this->load->library('form_validation');
		$this->load->model('users_model', 'user');
		$this->load->model('images_model', 'image');
		$this->form_validation->set_rules('image_id', 'Img Id', 'required|numeric|exists[image.id]');

		if ($this->form_validation->run())
		{
			if($this->user->add_fav($this->session->userdata('user_id'), $this->input->post('image_id')))
			{
				$this->session->set_flashdata('success', 'Image has been added to your favorites');
				redirect('/');
			}
			else
			{
				$this->session->set_flashdata('warning', 'Image might already be in favorites, Image not added!');
				redirect('/');
			}
		}
		else
		{
			//Let the user think we rated the image.
			redirect('/');
		}
	}

}

/* End of file image.php */
/* Location: ./application/controllers/image.php */