<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_User extends CI_Migration {

	public function up()
	{
		/**
		 * 
		 * Add columns
		 */
		$sql = "ALTER TABLE `user` 
					ADD `level` TINYINT( 2 ) UNSIGNED NOT NULL AFTER `name` ,
					ADD `passhash` BINARY( 20 ) NOT NULL AFTER `level` ,
					ADD `created` DATETIME NOT NULL AFTER `passhash` ,
					ADD `expiration` DATE NOT NULL AFTER `created`";
				
		$this->db->query($sql);
		
		/**
		 * 
		 * Create table
		 */
		$sql = "CREATE TABLE `user_image` (
				  `user_id` int(11) NOT NULL,
				  `image_id` int(11) NOT NULL,
				  UNIQUE KEY `user_id` (`user_id`,`image_id`),
				  KEY `image_id` (`image_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS  `session` (
					session_id varchar(40) DEFAULT '0' NOT NULL,
					ip_address varchar(16) DEFAULT '0' NOT NULL,
					user_agent varchar(120) NOT NULL,
					last_activity int(10) unsigned DEFAULT 0 NOT NULL,
					user_data text NOT NULL,
					PRIMARY KEY (session_id),
					KEY `last_activity_idx` (`last_activity`));";
				
		$this->db->query($sql);
		
		
		/**
		 * 
		 * Add foreign keys
		 */
		$sql = "ALTER TABLE `user_image`
				  ADD CONSTRAINT `user_image_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE,
				  ADD CONSTRAINT `user_image_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;";
		$this->db->query($sql);
		
		/**
		 * 
		 * Miscellaneous
		 */
		$sql = "ALTER TABLE `user` CHANGE `level` `level` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '1'";
		$this->db->query($sql);
		
	}

	public function down()
	{
		$sql = "ALTER TABLE `user` 
					DROP COLUMN `level` ,
					DROP COLUMN `passhash`,
					DROP COLUMN `created`,
					DROP COLUMN `expiration`";
				
		$this->db->query($sql);
		
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
		$this->dbforge->drop_table('user_image');
		$this->dbforge->drop_table('session');
		$this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
	}
}