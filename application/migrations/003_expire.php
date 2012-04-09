<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Expire extends CI_Migration {

	public function up()
	{
		/**
		 * 
		 * Change column type
		 */
		$sql = "ALTER TABLE `user` CHANGE `expiration` `expiration` DATETIME NOT NULL";
		$this->db->query($sql);
		
	}

	public function down()
	{
		$sql = "ALTER TABLE `user` CHANGE `expiration` `expiration` DATE NOT NULL";
		$this->db->query($sql);
	}
}