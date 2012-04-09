<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Queue extends CI_Migration {

	public function up()
	{
		$sql = "CREATE TABLE `image_queue` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `state` enum('O','A','C') NOT NULL DEFAULT 'O',
				  `image_id` int(11) NOT NULL,
				  `modified` datetime NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `image_id` (`image_id`),
				  CONSTRAINT `image_queue_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE
				) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8";
		$this->db->query($sql);
	}

	public function down()
	{
		$sql = 'DROP TABLE `image_queue`';
		$this->db->query($sql);
	}
}