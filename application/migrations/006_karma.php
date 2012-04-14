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
		
		$sql = "CREATE TABLE `rank` (
				  `title` varchar(50) NOT NULL,
				  `above_percentile` tinyint(3) NOT NULL,
				  UNIQUE KEY `rank` (`title`),
				  KEY `percentage` (`above_percentile`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		$this->db->query($sql);
		
		/**
		 * 
		 * Insert ranks
		 */
		$sql = "INSERT INTO `rank` (`title`, `above_percentile`) VALUES
					('Rankless', 0),
					('Private', 2),
					('Private 2', 4),
					('Private First Class', 5),
					('Specialist', 9),
					('Corporal', 22),
					('Sergeant', 34),
					('Second Lieutenant', 45),
					('First Lieutenant', 55),
					('Captain', 64),
					('Major', 72),
					('Lieutenant Colonel', 79),
					('Colonel', 85),
					('Brigadier General', 90),
					('Major General', 94),
					('Lieutenant General', 97),
					('General', 99)";
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
		
		$sql = "CREATE VIEW `subview_karma_count` AS select count(0) AS `r`,`subview_user_karma`.`karma` AS `karma` from `subview_user_karma` group by `subview_user_karma`.`karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `subview_karma_total` AS select count(0) AS `total` from `subview_user_karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `subview_karma_percentile` AS select sum(`g1`.`r`) AS `n`,`g2`.`karma` AS `karma`,((sum(`g1`.`r`) / `subview_karma_total`.`total`) * 100) AS `percentile` from ((`subview_karma_count` `g1` join `subview_karma_count` `g2`) join `subview_karma_total` on((`g1`.`karma` < `g2`.`karma`))) group by `g2`.`karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `view_user_rank` AS select `vuk`.`user_id` AS `user_id`,`vuk`.`karma` AS `karma`,`r`.`title` AS `title`,`skp`.`percentile` AS `percentile` from ((`view_user_karma` `vuk` join `subview_karma_percentile` `skp` on((`vuk`.`karma` = `skp`.`karma`))) join `rank` `r` on(((`skp`.`percentile` > `r`.`above_percentile`) and (abs((`skp`.`percentile` - `r`.`above_percentile`)) <= 10)))) group by `vuk`.`user_id` order by `vuk`.`karma`";
		$this->db->query($sql);
		
		/**
		 * 
		 * Drop old columns
		 */
 		$sql = "ALTER TABLE user DROP COLUMN expiration";
		$this->db->query($sql);
		
 		$sql = "ALTER TABLE user DROP COLUMN level";
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

		$sql = 'DROP VIEW `subview_karma_percentile`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `subview_karma_total`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `subview_karma_count`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `view_user_rank`';
		$this->db->query($sql);
		
		$sql = 'DROP TABLE `karma`';
		$this->db->query($sql);
		
		$sql = 'DROP TABLE `rank`';
		$this->db->query($sql);
	}
}