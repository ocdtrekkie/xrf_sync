CREATE TABLE IF NOT EXISTS `y_nodes` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `pool_id` int(4) NOT NULL DEFAULT '0' COMMENT 'Identifies a group of nodes which can communicate, 0 is no pool',
  `descr` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Friendly name of node',
  `access_key` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Access key for node',
  `static` int(1) NOT NULL COMMENT '0 for clients and mobile, 1 for stationary machines',
  `last_seen` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Last check in date',
  `last_ip_addr` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP address at last check in',
  `user_agent` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User agent of node',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Devices participating in sync module';
