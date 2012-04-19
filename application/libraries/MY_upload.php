<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Exenstion File Uploading Class
 */

class MY_Upload extends CI_Upload {

	public function do_multi_upload($field = 'userfile')
	{
		$errors = NULL;
		$data = NULL;
		for($i = 0; $i < count($_FILES); $i++)
		{
			$_FILES['field_name']['name'] 		= $_FILES[$field]['name'][$i];
			$_FILES['field_name']['type'] 		= $_FILES[$field]['type'][$i];
			$_FILES['field_name']['tmp_name'] 	= $_FILES[$field]['tmp_name'][$i];
			$_FILES['field_name']['error'] 		= $_FILES[$field]['error'][$i];
			$_FILES['field_name']['size'] 		= $_FILES[$field]['size'][$i];

			$this->initialize();

			if($this->do_upload('field_name'))
			{
				$data[$i] = $this->data();
			}
			else
			{
				$errors[$i] = $this->display_errors();
			}
		}

		return array('errors' => $errors, 'data' => $data);
	}

}
?>