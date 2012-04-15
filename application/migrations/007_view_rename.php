<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Karma extends CI_Migration {

	public function up()
	{
		/**
		 * 
		 * Add Table
		 */
		$sql = "CREATE TABLE `user_data` (
				  `user_id` int(11) unsigned NOT NULL,
				  `title` varchar(50) NOT NULL,
				  `percentile` decimal(10,0) NOT NULL,
				  `karma` decimal(10,0) NOT NULL,
				  `rank` int(11) NOT NULL,
				  PRIMARY KEY (`user_id`),
				  KEY `karma` (`karma`)
				) ENGINE=MEMORY DEFAULT CHARSET=latin1";
		$this->db->query($sql);
		
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
		
		
		$sql = "CREATE VIEW `vw_karma_concat` AS SELECT group_concat( DISTINCT `vw_user_karma`.`karma` ORDER BY `vw_user_karma`.`karma` DESC SEPARATOR ',' ) AS `karma_concat` FROM `vw_user_karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `vw_karma_count` AS select count(0) AS `r`,`vw_user_karma`.`karma` AS `karma` from `vw_user_karma` group by `vw_user_karma`.`karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `vw_karma_percentile` AS select sum(`t1`.`r`) AS `n`,`t2`.`karma` AS `karma`,((sum(`t1`.`r`) / `t3`.`total`) * 100) AS `percentile` from ((`vw_karma_count` `t1` join `vw_karma_count` `t2` on((`t1`.`karma` < `t2`.`karma`))) join `vw_karma_total` `t3`) group by `t2`.`karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `vw_karma_total` AS select count(0) AS `total` from `vw_user_karma`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `vw_user_karma` AS select `u`.`id` AS `user_id`,sum(`k`.`karma`) AS `karma` from (`user` `u` left join `karma` `k` on((`u`.`id` = `k`.`user_id`))) where ((`k`.`given` > (now() - interval 6 month)) or isnull(`k`.`given`)) group by `u`.`id`";
		$this->db->query($sql);
		
		$sql = "CREATE VIEW `vw_user_title` AS select `uk`.`user_id` AS `user_id`,`r`.`title` AS `title`,`kp`.`percentile` AS `percentile`,`uk`.`karma` AS `karma` from ((`vw_user_karma` `uk` join `vw_karma_percentile` `kp` on((`uk`.`karma` = `kp`.`karma`))) left join `rank` `r` on((`kp`.`percentile` >= `r`.`above_percentile`))) order by `uk`.`user_id`,`r`.`above_percentile` desc";
		$this->db->query($sql);	
	}

	public function down()
	{
		$sql = 'DROP VIEW `vw_karma_concat`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `vw_karma_count`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `vw_karma_percentile`';
		$this->db->query($sql);

		$sql = 'DROP VIEW `vw_karma_total`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `vw_user_karma`';
		$this->db->query($sql);
		
		$sql = 'DROP VIEW `vw_user_title`';
		$this->db->query($sql);
		
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
		
		$sql = 'DROP TABLE `user_data`';
		$this->db->query($sql);
	}
}