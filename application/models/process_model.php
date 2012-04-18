<?php
class Process_model extends CI_Model {

	var $image_id;
	public function image($upload_data)
	{

		$hash = @sha1_file($upload_data['full_path']);
		if(strlen($hash) != 40)
		{
			show_error('Unable to get the hash of the file');
			unlink($upload_data['full_path']);
			return FALSE;
		}

		//Insert everything in the database
		$this->db->trans_begin();
		$sql = "SELECT id FROM image WHERE LOWER(HEX(hash)) = ?";
		$query = $this->db->query($sql, $hash);
		if($query->num_rows() > 0)
		{
			$row = $query->row_array();
			$this->image_id = $row['id'];
		}
		else
		{
			//No duplicate found
			$this->db->set('hash', 'UNHEX(\'' . $hash . '\')', FALSE);
			$this->db->set('uploaded', date("Y-m-d H:i:s"));
			$this->db->insert('image');
			$this->image_id = $this->db->insert_id();
			$this->store_data($upload_data);
	
			//Try to move the uploaded file
			if( ! $this->move_file($upload_data['full_path']))
			{
				//Could not move the file, rollback the database inserts
				$this->db->trans_rollback();
				return FALSE;
			}
			
			//Add the image to the queue
			$this->load->model('queue_model', 'queue');
			$this->queue->add($this->image_id);
		}

		//Bind the images to the user, use IGNORE because the user could already be bind to that particular image
		$this->db->query('INSERT IGNORE INTO user_image (user_id, image_id) VALUES (?, ?)', array($this->auth->get_user_id(), $this->image_id));

		if($this->db->trans_status() === FALSE)
		{
			//Database inserts failed, removing the file if it has moved is unnessesery, it
			// will be overwritten on the nex upload.
			$this->db->trans_rollback();
			return FALSE;
		}
		else
		{
			$this->db->trans_commit();
			return $this->image_id;
		}
	}

	private function store_data($upload_data)
	{
		$exif = $this->exif($upload_data['full_path']);

		$data = array('image_id' => $this->image_id, 'lat' => $exif['lat'], 'lng' => $exif['lng'], 'width' => $upload_data['image_width'], 'height' => $upload_data['image_height'], 'created' => $exif['created'], 'mime' => $upload_data['file_type'], 'size' => $upload_data['file_size']);
		$this->db->insert('image_data', $data);
		
		$this->load->model('images_model', 'image');
		if($exif['make'])
		{
			$this->image->add_tag($this->image_id, $exif['make']);
		}
		
		if($exif['model'])
		{
			$this->image->add_tag($this->image_id, $exif['model']);
		}
	}

	private function exif($file)
	{
		//http://www.php.net/manual/en/function.exif-read-data.php#107888
		if((isset($imagePath)) and (file_exists($imagePath)))
		{

			$exif = @read_exif_data($imagePath, 'IFD0', 0);

			//Make
			if(@array_key_exists('Make', $exif))
			{
				$return['make'] = $exif['Make'];
			}
			else
			{
				$return['make'] = FALSE;
			}

			// Model
			if(@array_key_exists('Model', $exif))
			{
				$return['model'] = $exif['Model'];
			}
			else
			{
				$return['model'] = FALSE;
			}

			// Date
			if(@array_key_exists('DateTime', $exif))
			{
				$return['created'] = $exif['DateTime'];
			}
			else
			{
				$return['created'] = NULL;
			}

			$geo = $this->geo($file);

			$return['lat'] = $geo['lat'];
			$return['lng'] = $geo['lng'];
			return $return;
		}
	}

	private function geo($file)
	{
		$exif = @exif_read_data($file, 0, true);

		if( ! $exif || $exif['GPS']['GPSLatitude'] == '')
		{
			return array('lat' => NULL, 'lon' => NULL);
		}
		else
		{
			//Lat
			$lat_ref = $exif['GPS']['GPSLatitudeRef'];
			$lat = $exif['GPS']['GPSLatitude'];
			list($num, $dec) = explode('/', $lat[0]);
			$lat_s = $num / $dec;
			list($num, $dec) = explode('/', $lat[1]);
			$lat_m = $num / $dec;
			list($num, $dec) = explode('/', $lat[2]);
			$lat_v = $num / $dec;

			//Lon
			$lon_ref = $exif['GPS']['GPSLongitudeRef'];
			$lon = $exif['GPS']['GPSLongitude'];
			list($num, $dec) = explode('/', $lon[0]);
			$lon_s = $num / $dec;
			list($num, $dec) = explode('/', $lon[1]);
			$lon_m = $num / $dec;
			list($num, $dec) = explode('/', $lon[2]);
			$lon_v = $num / $dec;

			$lat_int = ($lat_s + $lat_m / 60.0 + $lat_v / 3600.0);
			//Check orientaiton of latitude and prefix with (-) if S
			$lat_int = ($lat_ref == "S") ? '-' . $lat_int : $lat_int;
			$lon_int = ($lon_s + $lon_m / 60.0 + $lon_v / 3600.0);
			//Check orientation of longitude and prefix with (-) if W
			$lon_int = ($lon_ref == "W") ? '-' . $lon_int : $lon_int;

			return array('lat' => $lat_int, 'lon' => $lon_int);
		}
	}

	private function move_file($file)
	{
		$this->load->helper('path');
		$path = path_to_image($this->image_id);

		//Attempt to create the dir
		if( ! is_dir($path))
		{
			if( ! mkdir($path, 0777, TRUE))
			{
				show_error('Could not create image directory');
				return FALSE;
			}
		}

		$dest = $path . '/' . $this->image_id;
		if( ! copy($file, $dest))
		{
			show_error('Failed to copy file');
			return FALSE;
		}
		unlink($file);
		return TRUE;
	}
}