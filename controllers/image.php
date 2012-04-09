if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class Image extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('path', 'url'));

		if( ! $this->auth->is_allowed())
		{
			$this->session->set_flashdata('warning', 'Your account has expired, upload an image to gain access again.');
			$this->session->set_flashdata('redirect', uri_string());
			redirect('/upload');
		}
		$this->load->view('flash');
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

}

/* End of file image.php */
/* Location: ./application/controllers/image.php */