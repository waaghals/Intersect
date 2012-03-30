<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image extends CI_Controller {
	
	public function resize($img_id, $width = 400, $height = 125)
	{
		$this->load->model('images_model', 'images');
		$image = $this->images->get_image($img_id);
		
		$config['image_library'] = 'gd2';
        $config['source_image'] = $image['path'];
        $config['maintain_ratio'] = TRUE;
        $config['dynamic_output'] = TRUE;
		$config['master_dim'] = 'auto';
		
		//width and height need to be specified or the image gets skewed
		if(is_numeric($width)) {
			$config['width'] = $width;
		} else {
			$config['width'] = 4000;
		}
		
		if(is_numeric($height)) {
			$config['height'] = $height;
		} else {
			$config['height'] = 4000;
		}

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
	}
	
}
/* End of file image.php */
/* Location: ./application/controllers/image.php */