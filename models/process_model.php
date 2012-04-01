<?php
class Process_model extends CI_Model {
	
	var $image_id;
	public function image($upload_data) {
		
		$md5 = @md5_file($upload_data['full_path']);
		if (strlen($md5) != 32) {
	        show_error('Unable to get the MD5 of the file');
			unlink($upload_data['full_path']);
	        return FALSE;
	    }
		
		//Calculate the cvec
		$cvec = puzzle_fill_cvec_from_file($upload_data['full_path']);
		if (empty($cvec)) {
	        show_error('Unable to compute image signature');
			unlink($upload_data['full_path']);
	        return FALSE;
		}
		
		
		$this->load->model('signature_model', 'signature');
		//Insert everything in the database
		$this->db->trans_begin();
		$this->image_id = $this->signature->save($md5, $cvec);
		$this->store_data($upload_data);
		
		//Try to move the uploaded file
		if( ! $this->move_file($upload_data['full_path'])) {
			//Could not move the file, rollback the database inserts
			$this->db->trans_rollback();
			return FALSE;
		}
		
		if ($this->db->trans_status() === FALSE) {
			//Database inserts failed, removing the file if it has moved is unnessesery, it will be overwritten on the nex upload.
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			return TRUE;
		}
	}
	
	private function store_data($upload_data) {
		$exif = $this->exif($upload_data['full_path']);
		
		$data = array(
			'image_id' 	=> $this->image_id,
		    'lat' 		=> $exif['lat'],
		    'lng' 		=> $exif['lng'],
		    'width' 	=> $upload_data['image_width'],
		    'height' 	=> $upload_data['image_height'],
		    'make' 		=> $exif['make'],
		    'model' 	=> $exif['model'],
		    'created' 	=> $exif['created'],
			'mime'		=> $upload_data['file_type'],
			'size'		=> $upload_data['file_size']);
		$this->db->insert('image_data', $data);
	}
	
	private function exif($file) {
		//http://www.php.net/manual/en/function.exif-read-data.php#107888
		if ((isset($imagePath)) and (file_exists($imagePath))) {
			     
			$exif = @read_exif_data($imagePath ,'IFD0' ,0);
			
			//Make
			if (@array_key_exists('Make', $exif)) {
				$return['make'] = $exif['Make'];
			} else { $return['make'] = NULL; }
	   
			// Model
			if (@array_key_exists('Model', $exif)) {
				$return['model'] = $exif['Model'];
			} else { $return['model'] = NULL; }
	   
			// Date
			if (@array_key_exists('DateTime', $exif)) {
				$return['created'] = $exif['DateTime'];
			} else { $return['created'] = NULL; }

			$geo = $this->geo($file);
			
			$return['lat'] = $geo['lat'];
			$return['lng'] = $geo['lng'];
		}
	}

	private function geo($file) {
		$exif = @exif_read_data($file, 0, true);
  
	    if(!$exif || $exif['GPS']['GPSLatitude'] == '')
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
	
	private function move_file($file) {
		$this->load->helper('path');
		$path = path_to_image($this->image_id);
		
		//Attempt to create the dir
		if( ! is_dir($path)) {
			if( ! mkdir($path, 0777, TRUE)) {
				show_error('Could not create image directory');
				return FALSE;
			}
		}
		
		$dest = $path . '/' . $this->image_id;
		if( ! copy($file, $dest)) {
			show_error('Failed to copy file');
			return FALSE;
		}
		unlink($file);
		return TRUE;
	}
}