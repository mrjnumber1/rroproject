--
-- Table structure for table `www_log_error`
--

CREATE TABLE IF NOT EXISTS `www_log_error` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` char(36) collate utf8_unicode_ci NOT NULL,
  `hit` int(10) unsigned default NULL,
  `error` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `hit` (`hit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `www_log_error`
--


-- --------------------------------------------------------

--
-- Table structure for table `www_log_hit`
--

CREATE TABLE IF NOT EXISTS `www_log_hit` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` char(36) collate utf8_unicode_ci NOT NULL,
  `ip` bigint(20) unsigned NOT NULL,
  `stamp` datetime NOT NULL,
  `domain` varchar(255) collate utf8_unicode_ci NOT NULL,
  `path` varchar(255) collate utf8_unicode_ci NOT NULL,
  `get` varchar(255) collate utf8_unicode_ci NOT NULL,
  `post` varchar(255) collate utf8_unicode_ci NOT NULL,
  `agent` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cookie` varchar(255) collate utf8_unicode_ci NOT NULL,
  `user` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `ip` (`ip`),
  KEY `domain` (`domain`),
  KEY `path` (`path`),
  KEY `get` (`get`),
  KEY `post` (`post`),
  KEY `user` (`user`),
  KEY `stamp` (`stamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `www_log_hit`
--