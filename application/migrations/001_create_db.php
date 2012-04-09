<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_db extends CI_Migration {

	public function up()
	{
		/**
		 * 
		 * Create tables
		 */
		$sql = "CREATE TABLE `image` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `digest` char(32) NOT NULL,
				  `uploaded` datetime NOT NULL,
				  `rating` decimal(18,10) NOT NULL DEFAULT '1200.0000000000',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `digest` (`digest`),
				  KEY `rating` (`rating`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1000;";
				
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `image_data` (
				  `image_id` int(11) NOT NULL DEFAULT '0',
				  `lat` float(16,12) DEFAULT NULL,
				  `lng` float(16,12) DEFAULT NULL,
				  `width` int(4) NOT NULL,
				  `height` int(4) NOT NULL,
				  `make` varchar(50) DEFAULT NULL,
				  `model` varchar(100) DEFAULT NULL,
				  `created` datetime DEFAULT NULL,
				  `mime` varchar(15) DEFAULT NULL,
				  `size` int(10) unsigned DEFAULT NULL,
				  PRIMARY KEY (`image_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `image_tag_map` (
				  `image_id` int(11) NOT NULL,
				  `tag_id` int(11) NOT NULL,
				  UNIQUE KEY `image_id` (`image_id`,`tag_id`),
				  KEY `tag_id` (`tag_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `signature` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `compressed_signature` char(182) NOT NULL,
				  `image_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `image_id` (`image_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `tag` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `tag` varchar(50) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `tag` (`tag`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `tag_graph` (
				  `latch` smallint(5) unsigned DEFAULT NULL,
				  `origid` bigint(20) unsigned DEFAULT NULL,
				  `destid` bigint(20) unsigned DEFAULT NULL,
				  `weight` double DEFAULT NULL,
				  `seq` bigint(20) unsigned DEFAULT NULL,
				  `linkid` bigint(20) unsigned DEFAULT NULL,
				  KEY `latch` (`latch`,`origid`,`destid`) USING HASH,
				  KEY `latch_2` (`latch`,`destid`,`origid`) USING HASH
				) ENGINE=OQGRAPH DEFAULT CHARSET=latin1;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `user` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(70) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `name` (`name`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE `word` (
				  `pos_and_word` char(5) NOT NULL,
				  `signature_id` int(11) NOT NULL,
				  KEY `pos_and_word` (`pos_and_word`),
				  KEY `signature_id` (`signature_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$this->db->query($sql);
		
		/**
		 * 
		 * Add foreign keys
		 */
		$sql = "ALTER TABLE `image_data`
					ADD CONSTRAINT `image_data_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE;";
		$this->db->query($sql);
		
		$sql = "ALTER TABLE `image_tag_map`
					ADD CONSTRAINT `image_tag_map_ibfk_4` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE,
					ADD CONSTRAINT `image_tag_map_ibfk_3` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE;";
		$this->db->query($sql);
		
		$sql = "ALTER TABLE `signature`
					ADD CONSTRAINT `signature_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE;";
		$this->db->query($sql);
		
		$sql = "ALTER TABLE `word`
					ADD CONSTRAINT `word_ibfk_1` FOREIGN KEY (`signature_id`) REFERENCES `signature` (`id`) ON DELETE CASCADE;";
		$this->db->query($sql);
	}

	public function down()
	{
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
		$this->dbforge->drop_table('image');
		$this->dbforge->drop_table('image_data');
		$this->dbforge->drop_table('image_tag_map');
		$this->dbforge->drop_table('signature');
		$this->dbforge->drop_table('tag');
		$this->dbforge->drop_table('tag_graph');
		$this->dbforge->drop_table('user');
		$this->dbforge->drop_table('word');
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
	}
}