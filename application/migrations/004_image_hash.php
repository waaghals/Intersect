<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Image_hash extends CI_Migration {

	public function up()
	{
		$sql = 'ALTER TABLE `image` CHANGE `digest` `hash` BINARY( 20 ) NOT NULL';
		$this->db->query($sql);
	}

	public function down()
	{
		$sql = 'ALTER TABLE `image` CHANGE `hash` `digest` CHAR( 32 ) NOT NULL';
		$this->db->query($sql);
	}
}