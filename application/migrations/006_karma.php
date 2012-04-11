<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Karma extends CI_Migration {

	public function up()
	{
		/**
		 * 
		 * Add Table
		 */
		$sql = "CREATE TABLE `karma` (
				  `user_id` int(11) NOT NULL,
				  `karma` smallint(6) NOT NULL,
				  `given` datetime NOT NULL,
				  KEY `user_id` (`user_id`),
				  CONSTRAINT `karma_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$this->db->query($sql);
		
		/**
		 * 
		 * Views
		 */
		$sql = "CREATE VIEW `subview_user_karma` AS select `u`.`id` AS `user_id`,sum(`k`.`karma`) AS `karma` from (`user` `u` join `karma` `k` on((`u`.`id` = `k`.`user_id`))) where (`k`.`given` > (now() - interval 2 month)) group by `u`.`id`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `subview_karma_concat` AS select group_concat(distinct `subview_user_karma`.`karma` order by `subview_user_karma`.`karma` DESC separator ',') AS `karma_concat` from `subview_user_karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `view_user_karma` AS select `subview_user_karma`.`user_id` AS `user_id`,`subview_user_karma`.`karma` AS `karma`,find_in_set(`subview_user_karma`.`karma`,`subview_karma_concat`.`karma_concat`) AS `rank` from (`subview_user_karma` join `subview_karma_concat`)";
		$this->db->query($sql);
	}

	public function down()
	{
		$sql = 'DROP VIEW `view_user_karma`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `subview_karma_concat`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `subview_user_karma`';
		$this->db->query($sql);
		
		$sql = 'DROP TABLE `karma`';
		$this->db->query($sql);
	}
}