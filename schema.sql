--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `digest` char(32) NOT NULL,
  `extension` varchar(4) NOT NULL,
  `datetime` datetime NOT NULL,
  `rating` decimal(18,10) NOT NULL DEFAULT '1200.0000000000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `digest` (`digest`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20501 ;

-- --------------------------------------------------------

--
-- Table structure for table `image_data`
--

DROP TABLE IF EXISTS `image_data`;
CREATE TABLE IF NOT EXISTS `image_data` (
  `image_id` int(11) NOT NULL DEFAULT '0',
  `lat` float(16,12) DEFAULT NULL,
  `lng` float(16,12) DEFAULT NULL,
  `width` int(4) NOT NULL,
  `height` int(4) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image_tag_map`
--

DROP TABLE IF EXISTS `image_tag_map`;
CREATE TABLE IF NOT EXISTS `image_tag_map` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  UNIQUE KEY `image_id` (`image_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `signature`
--

DROP TABLE IF EXISTS `signature`;
CREATE TABLE IF NOT EXISTS `signature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compressed_signature` char(182) NOT NULL,
  `image_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20501 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag_graph`
--

DROP TABLE IF EXISTS `tag_graph`;
CREATE TABLE IF NOT EXISTS `tag_graph` (
  `latch` smallint(5) unsigned DEFAULT NULL,
  `origid` bigint(20) unsigned DEFAULT NULL,
  `destid` bigint(20) unsigned DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `seq` bigint(20) unsigned DEFAULT NULL,
  `linkid` bigint(20) unsigned DEFAULT NULL,
  KEY `latch` (`latch`,`origid`,`destid`) USING HASH,
  KEY `latch_2` (`latch`,`destid`,`origid`) USING HASH
) ENGINE=OQGRAPH DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `word`
--

DROP TABLE IF EXISTS `word`;
CREATE TABLE IF NOT EXISTS `word` (
  `pos_and_word` char(5) NOT NULL,
  `signature_id` int(11) NOT NULL,
  KEY `pos_and_word` (`pos_and_word`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `image_tag_map`
--
ALTER TABLE `image_tag_map`
  ADD CONSTRAINT `image_tag_map_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`),
  ADD CONSTRAINT `image_tag_map_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`);