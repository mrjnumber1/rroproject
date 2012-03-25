
CREATE TABLE IF NOT EXISTS `bg_skillcount` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bg_log` (
  `id` int(11) NOT NULL auto_increment,
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `killer` varchar(25) NOT NULL,
  `killer_id` int(11) NOT NULL,
  `killed` varchar(25) NOT NULL,
  `killed_id` int(11) NOT NULL,
  `map` varchar(11) NOT NULL default '',
  `skill` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `killer_id` (`killer_id`),
  KEY `killed_id` (`killed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `bg_stats` (
  `char_id` int(11) NOT NULL,
  `top_damage` int(11) NOT NULL default '0',
  `damage_done` int(11) unsigned NOT NULL default '0',
  `damage_received` int(11) unsigned NOT NULL default '0',
  `emperium_damage` int(11) unsigned NOT NULL default '0',
  `barricade_damage` int(11) unsigned NOT NULL default '0',
  `gstone_damage` int(11) unsigned NOT NULL default '0',
  `emperium_kill` int(11) NOT NULL default '0',
  `barricade_kill` int(11) NOT NULL default '0',
  `gstone_kill` int(11) NOT NULL default '0',
  `cq_wins` int(11) NOT NULL default '0',
  `cq_lost` int(11) NOT NULL default '0',
  `kill_count` int(11) NOT NULL default '0',
  `death_count` int(11) NOT NULL default '0',
  `win` int(11) NOT NULL default '0',
  `lost` int(11) NOT NULL default '0',
  `tie` int(11) NOT NULL default '0',
  `leader_win` int(11) NOT NULL default '0',
  `leader_lost` int(11) NOT NULL default '0',
  `leader_tie` int(11) NOT NULL default '0',
  `deserter` int(11) NOT NULL default '0',
  `sp_heal_potions` int(11) NOT NULL default '0',
  `hp_heal_potions` int(11) NOT NULL default '0',
  `yellow_gemstones` int(11) NOT NULL default '0',
  `red_gemstones` int(11) NOT NULL default '0',
  `blue_gemstones` int(11) NOT NULL default '0',
  `poison_bottles` int(11) NOT NULL default '0',
  `acid_demonstration` int(11) NOT NULL default '0',
  `acid_demonstration_fail` int(11) NOT NULL default '0',
  `support_skills_used` int(11) NOT NULL default '0',
  `healing_done` int(11) NOT NULL default '0',
  `wrong_support_skills_used` int(11) NOT NULL default '0',
  `wrong_healing_done` int(11) NOT NULL default '0',
  `sp_used` int(11) NOT NULL default '0',
  `zeny_used` int(11) NOT NULL default '0',
  `spiritb_used` int(11) NOT NULL default '0',
  `ammo_used` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `rank_points` int(11) NOT NULL default '0',
  `rank_games` int(11) NOT NULL default '0',
  PRIMARY KEY  (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `guild_rank` (
  `guild_id` int(11) NOT NULL,
  `castle_id` int(11) NOT NULL,
  `capture` int(11) unsigned NOT NULL default '0',
  `emperium` int(11) unsigned NOT NULL default '0',
  `treasure` int(11) unsigned NOT NULL default '0',
  `top_eco` int(11) unsigned NOT NULL default '0',
  `top_def` int(11) unsigned NOT NULL default '0',
  `invest_eco` int(11) unsigned NOT NULL default '0',
  `invest_def` int(11) unsigned NOT NULL default '0',
  `offensive_score` int(11) unsigned NOT NULL default '0',
  `defensive_score` int(11) unsigned NOT NULL default '0',
  `possession_time` int(11) unsigned NOT NULL default '0',
  `zeny_eco` int(11) unsigned NOT NULL default '0',
  `zeny_def` int(11) unsigned NOT NULL default '0',
  `skill_battleorder` int(11) unsigned NOT NULL default '0',
  `skill_regeneration` int(11) unsigned NOT NULL default '0',
  `skill_restore` int(11) unsigned NOT NULL default '0',
  `skill_emergencycall` int(11) unsigned NOT NULL default '0',
  `off_kill` int(11) unsigned NOT NULL default '0',
  `off_death` int(11) unsigned NOT NULL default '0',
  `def_kill` int(11) unsigned NOT NULL default '0',
  `def_death` int(11) unsigned NOT NULL default '0',
  `ext_kill` int(11) unsigned NOT NULL default '0',
  `ext_death` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`guild_id`,`castle_id`),
  KEY `castle_id` (`castle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `item_serials` (
  `nameid` smallint(5) unsigned NOT NULL,
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`nameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `email` varchar(39) NOT NULL default '',
  `sex` enum('M','F') NOT NULL default 'M',
  `country` varchar(20) NOT NULL default '',
  `msn` varchar(39) NOT NULL default '',
  `aim` varchar(15) NOT NULL default '',
  `mpass` varchar(40) NOT NULL default '',
  `last_ip` varchar(20) NOT NULL default '0.0.0.0',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `services` smallint(5) unsigned NOT NULL default '0',
  `banned` tinyint(1) unsigned NOT NULL default '0',
  `ref_option` tinyint(3) unsigned NOT NULL default '0',
  `ref_email` varchar(39) NOT NULL default 'none',
  `ref_points` smallint(5) unsigned NOT NULL default '0',
  `mlevel` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `member_storage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `member_id` int(11) unsigned NOT NULL default '0',
  `nameid` int(11) unsigned NOT NULL default '0',
  `amount` int(11) unsigned NOT NULL default '0',
  `equip` mediumint(8) unsigned NOT NULL default '0',
  `identify` smallint(6) unsigned NOT NULL default '0',
  `refine` tinyint(3) unsigned NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `card0` smallint(11) NOT NULL default '0',
  `card1` smallint(11) NOT NULL default '0',
  `card2` smallint(11) NOT NULL default '0',
  `card3` smallint(11) NOT NULL default '0',
  `expire_time` int(11) unsigned NOT NULL default '0',
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `woe_log` (
  `id` int(11) NOT NULL auto_increment,
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `killer` varchar(25) NOT NULL,
  `killer_id` int(11) NOT NULL,
  `killed` varchar(25) NOT NULL,
  `killed_id` int(11) NOT NULL,
  `map` varchar(11) NOT NULL default '',
  `skill` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `killer_id` (`killer_id`),
  KEY `killed_id` (`killed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `woe_skillcount` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `woe_stats` (
  `char_id` int(11) NOT NULL,
  `kill_count` int(11) NOT NULL default '0',
  `death_count` int(11) NOT NULL default '0',
  `top_damage` int(11) NOT NULL default '0',
  `damage_done` int(11) unsigned NOT NULL default '0',
  `damage_received` int(11) unsigned NOT NULL default '0',
  `emperium_damage` int(11) unsigned NOT NULL default '0',
  `guardian_damage` int(11) unsigned NOT NULL default '0',
  `barricade_damage` int(11) unsigned NOT NULL default '0',
  `gstone_damage` int(11) unsigned NOT NULL default '0',
  `emperium_kill` int(11) NOT NULL default '0',
  `guardian_kill` int(11) NOT NULL default '0',
  `barricade_kill` int(11) NOT NULL default '0',
  `gstone_kill` int(11) NOT NULL default '0',
  `sp_heal_potions` int(11) NOT NULL default '0',
  `hp_heal_potions` int(11) NOT NULL default '0',
  `yellow_gemstones` int(11) NOT NULL default '0',
  `red_gemstones` int(11) NOT NULL default '0',
  `blue_gemstones` int(11) NOT NULL default '0',
  `poison_bottles` int(11) NOT NULL default '0',
  `acid_demonstration` int(11) NOT NULL default '0',
  `acid_demonstration_fail` int(11) NOT NULL default '0',
  `support_skills_used` int(11) NOT NULL default '0',
  `healing_done` int(11) NOT NULL default '0',
  `wrong_support_skills_used` int(11) NOT NULL default '0',
  `wrong_healing_done` int(11) NOT NULL default '0',
  `sp_used` int(11) NOT NULL default '0',
  `zeny_used` int(11) NOT NULL default '0',
  `spiritb_used` int(11) NOT NULL default '0',
  `ammo_used` int(11) NOT NULL default '0',
  PRIMARY KEY  (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `www_log_error` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` char(36) collate utf8_unicode_ci NOT NULL,
  `hit` int(10) unsigned default NULL,
  `error` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `hit` (`hit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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
