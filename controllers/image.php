<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image extends CI_Controller {
	
	public function resize($img_id, $width = 400, $height = 250)
	{
		$this->output->cache(60*60*24);
		$this->load->helper('path');

		$config['image_library'] 	= 'gd2';
        $config['source_image'] 	= path_to_image($img_id) . $img_id;
        $config['maintain_ratio'] 	= TRUE;
        $config['dynamic_output'] 	= TRUE;
		$config['master_dim'] 		= 'auto';

		//width and height need to be specified or the image gets skewed
		if(is_numeric($width)) {
			$config['width'] = $width;
		} else {
			$config['width'] = $this->config->item('max_width');
		}
		
		if(is_numeric($height)) {
			$config['height'] = $height;
		} else {
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