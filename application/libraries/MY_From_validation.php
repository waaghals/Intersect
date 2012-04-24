<?php
if( ! defined('BASEPATH'))
	exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	public function exists($str, $field)
	{
		if(substr_count($field, '.') == 3)
		{
			list($table, $field, $id_field, $id_val) = explode('.', $field);
			$query = $this->CI->db->limit(1)->where($field, $str)->where($id_field . ' != ', $id_val)->get($table);
		}
		else
		{
			list($table, $field) = explode('.', $field);
			$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		}

		return $query->num_rows() !== 0;
	}
	
	/**
	 * Valid tags
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_tags($str)
	{
		$tags = explode(',', $tags);

		foreach($tags as $tag)
		{
			if( ! preg_match('(^[a-zA-Z0-9 ]{2,25}$)', $tag))
			{
				return FALSE;
			}
		}
		return TRUE;
	}
}
/* End of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */