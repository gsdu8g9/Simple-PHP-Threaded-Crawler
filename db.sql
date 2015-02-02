-- ----------------------------
-- Table structure for urls
-- ----------------------------
CREATE TABLE `urls` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`url` varchar(255) NOT NULL,
		`checked` tinyint(4) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB;
