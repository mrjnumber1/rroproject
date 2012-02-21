--
-- Table structure for table `auction`
--

CREATE TABLE IF NOT EXISTS `auction` (
  `auction_id` bigint(20) unsigned NOT NULL auto_increment,
  `seller_id` int(11) unsigned NOT NULL default '0',
  `seller_name` varchar(30) NOT NULL default '',
  `buyer_id` int(11) unsigned NOT NULL default '0',
  `buyer_name` varchar(30) NOT NULL default '',
  `price` int(11) unsigned NOT NULL default '0',
  `buynow` int(11) unsigned NOT NULL default '0',
  `hours` smallint(6) NOT NULL default '0',
  `timestamp` int(11) unsigned NOT NULL default '0',
  `nameid` int(11) unsigned NOT NULL default '0',
  `item_name` varchar(50) NOT NULL default '',
  `type` smallint(6) NOT NULL default '0',
  `refine` tinyint(3) unsigned NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `card0` smallint(11) NOT NULL default '0',
  `card1` smallint(11) NOT NULL default '0',
  `card2` smallint(11) NOT NULL default '0',
  `card3` smallint(11) NOT NULL default '0',
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`auction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `auction`
--


-- --------------------------------------------------------

--
-- Table structure for table `bg_skillcount`
--

CREATE TABLE IF NOT EXISTS `bg_skillcount` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bg_skillcount`
--


-- --------------------------------------------------------

--
-- Table structure for table `cart_inventory`
--

CREATE TABLE IF NOT EXISTS `cart_inventory` (
  `id` int(11) NOT NULL auto_increment,
  `char_id` int(11) NOT NULL default '0',
  `nameid` int(11) NOT NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `equip` mediumint(8) unsigned NOT NULL default '0',
  `identify` smallint(6) NOT NULL default '0',
  `refine` tinyint(3) unsigned NOT NULL default '0',
  `attribute` tinyint(4) NOT NULL default '0',
  `card0` smallint(11) NOT NULL default '0',
  `card1` smallint(11) NOT NULL default '0',
  `card2` smallint(11) NOT NULL default '0',
  `card3` smallint(11) NOT NULL default '0',
  `price` tinyint(8) unsigned NOT NULL default '0',
  `expire_time` int(11) unsigned NOT NULL default '0',
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cart_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `char`
--

CREATE TABLE IF NOT EXISTS `char` (
  `char_id` int(11) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL default '0',
  `char_num` tinyint(1) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `class` smallint(6) unsigned NOT NULL default '0',
  `base_level` smallint(6) unsigned NOT NULL default '1',
  `job_level` smallint(6) unsigned NOT NULL default '1',
  `base_exp` bigint(20) unsigned NOT NULL default '0',
  `job_exp` bigint(20) unsigned NOT NULL default '0',
  `zeny` int(11) unsigned NOT NULL default '0',
  `str` smallint(4) unsigned NOT NULL default '0',
  `agi` smallint(4) unsigned NOT NULL default '0',
  `vit` smallint(4) unsigned NOT NULL default '0',
  `int` smallint(4) unsigned NOT NULL default '0',
  `dex` smallint(4) unsigned NOT NULL default '0',
  `luk` smallint(4) unsigned NOT NULL default '0',
  `max_hp` mediumint(8) unsigned NOT NULL default '0',
  `hp` mediumint(8) unsigned NOT NULL default '0',
  `max_sp` mediumint(6) unsigned NOT NULL default '0',
  `sp` mediumint(6) unsigned NOT NULL default '0',
  `status_point` int(11) unsigned NOT NULL default '0',
  `skill_point` int(11) unsigned NOT NULL default '0',
  `option` int(11) NOT NULL default '0',
  `karma` tinyint(3) NOT NULL default '0',
  `manner` smallint(6) NOT NULL default '0',
  `party_id` int(11) unsigned NOT NULL default '0',
  `guild_id` int(11) unsigned NOT NULL default '0',
  `pet_id` int(11) unsigned NOT NULL default '0',
  `homun_id` int(11) unsigned NOT NULL default '0',
  `hair` tinyint(4) unsigned NOT NULL default '0',
  `hair_color` smallint(5) unsigned NOT NULL default '0',
  `clothes_color` smallint(5) unsigned NOT NULL default '0',
  `weapon` smallint(6) unsigned NOT NULL default '0',
  `shield` smallint(6) unsigned NOT NULL default '0',
  `head_top` smallint(6) unsigned NOT NULL default '0',
  `head_mid` smallint(6) unsigned NOT NULL default '0',
  `head_bottom` smallint(6) unsigned NOT NULL default '0',
  `robe` smallint(6) unsigned NOT NULL default '0',
  `last_map` varchar(11) NOT NULL default '',
  `last_x` smallint(4) unsigned NOT NULL default '53',
  `last_y` smallint(4) unsigned NOT NULL default '111',
  `save_map` varchar(11) NOT NULL default '',
  `save_x` smallint(4) unsigned NOT NULL default '53',
  `save_y` smallint(4) unsigned NOT NULL default '111',
  `partner_id` int(11) unsigned NOT NULL default '0',
  `online` tinyint(2) NOT NULL default '0',
  `father` int(11) unsigned NOT NULL default '0',
  `mother` int(11) unsigned NOT NULL default '0',
  `child` int(11) unsigned NOT NULL default '0',
  `fame` int(11) unsigned NOT NULL default '0',
  `rename` smallint(3) unsigned NOT NULL default '0',
  `delete_date` int(11) unsigned NOT NULL default '0',
  `playtime` bigint(20) unsigned NOT NULL default '0',
  `lang` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`),
  KEY `account_id` (`account_id`),
  KEY `party_id` (`party_id`),
  KEY `guild_id` (`guild_id`),
  KEY `name` (`name`),
  KEY `online` (`online`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150000 ;

--
-- Dumping data for table `char`
--

-- --------------------------------------------------------

--
-- Table structure for table `charlog`
--

CREATE TABLE IF NOT EXISTS `charlog` (
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `char_msg` varchar(255) NOT NULL default 'char select',
  `account_id` int(11) NOT NULL default '0',
  `char_num` tinyint(4) NOT NULL default '0',
  `name` varchar(23) NOT NULL default '',
  `str` int(11) unsigned NOT NULL default '0',
  `agi` int(11) unsigned NOT NULL default '0',
  `vit` int(11) unsigned NOT NULL default '0',
  `int` int(11) unsigned NOT NULL default '0',
  `dex` int(11) unsigned NOT NULL default '0',
  `luk` int(11) unsigned NOT NULL default '0',
  `hair` tinyint(4) NOT NULL default '0',
  `hair_color` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `charlog`
--

-- --------------------------------------------------------

--
-- Table structure for table `char_bg_log`
--

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

--
-- Dumping data for table `char_bg_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `char_bg_stats`
--

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
  `acid_demostration` int(11) NOT NULL default '0',
  `acid_demostration_fail` int(11) NOT NULL default '0',
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

--
-- Dumping data for table `char_bg_stats`
--

-- --------------------------------------------------------

--
-- Table structure for table `char_woe_log`
--

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

--
-- Dumping data for table `char_woe_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `char_woe_stats`
--

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
  `acid_demostration` int(11) NOT NULL default '0',
  `acid_demostration_fail` int(11) NOT NULL default '0',
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

--
-- Dumping data for table `char_woe_stats`
--


-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `char_id` int(11) NOT NULL default '0',
  `friend_account` int(11) NOT NULL default '0',
  `friend_id` int(11) NOT NULL default '0',
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `friends`
--


-- --------------------------------------------------------

--
-- Table structure for table `global_reg_value`
--

CREATE TABLE IF NOT EXISTS `global_reg_value` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `str` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '0',
  `type` tinyint(1) unsigned NOT NULL default '3',
  `account_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`str`,`account_id`),
  KEY `account_id` (`account_id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `global_reg_value`
--


-- --------------------------------------------------------

--
-- Table structure for table `guild`
--

CREATE TABLE IF NOT EXISTS `guild` (
  `guild_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(24) NOT NULL default '',
  `char_id` int(11) unsigned NOT NULL default '0',
  `master` varchar(24) NOT NULL default '',
  `guild_lv` tinyint(6) unsigned NOT NULL default '0',
  `connect_member` tinyint(6) unsigned NOT NULL default '0',
  `max_member` tinyint(6) unsigned NOT NULL default '0',
  `average_lv` smallint(6) unsigned NOT NULL default '1',
  `exp` bigint(20) unsigned NOT NULL default '0',
  `next_exp` int(11) unsigned NOT NULL default '0',
  `skill_point` tinyint(11) unsigned NOT NULL default '0',
  `mes1` varchar(60) NOT NULL default '',
  `mes2` varchar(120) NOT NULL default '',
  `emblem_len` int(11) unsigned NOT NULL default '0',
  `emblem_id` int(11) unsigned NOT NULL default '0',
  `emblem_data` blob,
  PRIMARY KEY  (`guild_id`,`char_id`),
  UNIQUE KEY `guild_id` (`guild_id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `guild`
--
-- --------------------------------------------------------

--
-- Table structure for table `guild_alliance`
--

CREATE TABLE IF NOT EXISTS `guild_alliance` (
  `guild_id` int(11) unsigned NOT NULL default '0',
  `opposition` int(11) unsigned NOT NULL default '0',
  `alliance_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  PRIMARY KEY  (`guild_id`,`alliance_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_alliance`
--


-- --------------------------------------------------------

--
-- Table structure for table `guild_castle`
--

CREATE TABLE IF NOT EXISTS `guild_castle` (
  `castle_id` int(11) unsigned NOT NULL default '0',
  `guild_id` int(11) unsigned NOT NULL default '0',
  `economy` int(11) unsigned NOT NULL default '0',
  `defense` int(11) unsigned NOT NULL default '0',
  `triggerE` int(11) unsigned NOT NULL default '0',
  `triggerD` int(11) unsigned NOT NULL default '0',
  `nextTime` int(11) unsigned NOT NULL default '0',
  `payTime` int(11) unsigned NOT NULL default '0',
  `createTime` int(11) unsigned NOT NULL default '0',
  `visibleC` int(11) unsigned NOT NULL default '0',
  `visibleG0` int(11) unsigned NOT NULL default '0',
  `visibleG1` int(11) unsigned NOT NULL default '0',
  `visibleG2` int(11) unsigned NOT NULL default '0',
  `visibleG3` int(11) unsigned NOT NULL default '0',
  `visibleG4` int(11) unsigned NOT NULL default '0',
  `visibleG5` int(11) unsigned NOT NULL default '0',
  `visibleG6` int(11) unsigned NOT NULL default '0',
  `visibleG7` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`castle_id`),
  KEY `guild_id` (`guild_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_castle`
--

-- --------------------------------------------------------

--
-- Table structure for table `guild_expulsion`
--

CREATE TABLE IF NOT EXISTS `guild_expulsion` (
  `guild_id` int(11) unsigned NOT NULL default '0',
  `account_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  `mes` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`guild_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_expulsion`
--


-- --------------------------------------------------------

--
-- Table structure for table `guild_member`
--

CREATE TABLE IF NOT EXISTS `guild_member` (
  `guild_id` int(11) unsigned NOT NULL default '0',
  `account_id` int(11) unsigned NOT NULL default '0',
  `char_id` int(11) unsigned NOT NULL default '0',
  `hair` tinyint(6) unsigned NOT NULL default '0',
  `hair_color` smallint(6) unsigned NOT NULL default '0',
  `gender` tinyint(6) unsigned NOT NULL default '0',
  `class` smallint(6) unsigned NOT NULL default '0',
  `lv` smallint(6) unsigned NOT NULL default '0',
  `exp` bigint(20) unsigned NOT NULL default '0',
  `exp_payper` tinyint(11) unsigned NOT NULL default '0',
  `online` tinyint(4) unsigned NOT NULL default '0',
  `position` tinyint(6) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  PRIMARY KEY  (`guild_id`,`char_id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_member`
--

-- --------------------------------------------------------

--
-- Table structure for table `guild_position`
--

CREATE TABLE IF NOT EXISTS `guild_position` (
  `guild_id` int(9) unsigned NOT NULL default '0',
  `position` tinyint(6) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  `mode` tinyint(11) unsigned NOT NULL default '0',
  `exp_mode` tinyint(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`guild_id`,`position`),
  KEY `guild_id` (`guild_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_position`
--

-- --------------------------------------------------------

--
-- Table structure for table `guild_rank`
--

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

--
-- Dumping data for table `guild_rank`
--

-- --------------------------------------------------------

--
-- Table structure for table `guild_skill`
--

CREATE TABLE IF NOT EXISTS `guild_skill` (
  `guild_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `lv` tinyint(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`guild_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `guild_skill`
--

-- --------------------------------------------------------

--
-- Table structure for table `guild_storage`
--

CREATE TABLE IF NOT EXISTS `guild_storage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `guild_id` int(11) unsigned NOT NULL default '0',
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
  KEY `guild_id` (`guild_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `guild_storage`
--


-- --------------------------------------------------------

--
-- Table structure for table `homunculus`
--

CREATE TABLE IF NOT EXISTS `homunculus` (
  `homun_id` int(11) NOT NULL auto_increment,
  `char_id` int(11) NOT NULL,
  `class` mediumint(9) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  `level` smallint(4) NOT NULL default '0',
  `exp` int(12) NOT NULL default '0',
  `intimacy` int(12) NOT NULL default '0',
  `hunger` smallint(4) NOT NULL default '0',
  `str` smallint(4) unsigned NOT NULL default '0',
  `agi` smallint(4) unsigned NOT NULL default '0',
  `vit` smallint(4) unsigned NOT NULL default '0',
  `int` smallint(4) unsigned NOT NULL default '0',
  `dex` smallint(4) unsigned NOT NULL default '0',
  `luk` smallint(4) unsigned NOT NULL default '0',
  `hp` int(12) NOT NULL default '1',
  `max_hp` int(12) NOT NULL default '1',
  `sp` int(12) NOT NULL default '1',
  `max_sp` int(12) NOT NULL default '1',
  `skill_point` smallint(4) unsigned NOT NULL default '0',
  `alive` tinyint(2) NOT NULL default '1',
  `rename_flag` tinyint(2) NOT NULL default '0',
  `vaporize` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`homun_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `homunculus`
--


-- --------------------------------------------------------

--
-- Table structure for table `hotkey`
--

CREATE TABLE IF NOT EXISTS `hotkey` (
  `char_id` int(11) NOT NULL,
  `hotkey` tinyint(2) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `itemskill_id` int(11) unsigned NOT NULL default '0',
  `skill_lvl` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`hotkey`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `hotkey`
--

-- --------------------------------------------------------

--
-- Table structure for table `interlog`
--

CREATE TABLE IF NOT EXISTS `interlog` (
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `log` varchar(255) NOT NULL default ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `interlog`
--


-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `char_id` int(11) unsigned NOT NULL default '0',
  `nameid` int(11) unsigned NOT NULL default '0',
  `amount` int(11) unsigned NOT NULL default '0',
  `equip` mediumint(8) unsigned NOT NULL default '0',
  `identify` smallint(6) NOT NULL default '0',
  `refine` tinyint(3) unsigned NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `card0` smallint(11) NOT NULL default '0',
  `card1` smallint(11) NOT NULL default '0',
  `card2` smallint(11) NOT NULL default '0',
  `card3` smallint(11) NOT NULL default '0',
  `expire_time` int(11) unsigned NOT NULL default '0',
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `ipbanlist`
--

CREATE TABLE IF NOT EXISTS `ipbanlist` (
  `list` varchar(255) NOT NULL default '',
  `btime` datetime NOT NULL default '0000-00-00 00:00:00',
  `rtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `reason` varchar(255) NOT NULL default '',
  KEY `list` (`list`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ipbanlist`
--


-- --------------------------------------------------------

--
-- Table structure for table `item_serials`
--

CREATE TABLE IF NOT EXISTS `item_serials` (
  `nameid` smallint(5) unsigned NOT NULL,
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`nameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `item_serials`
--

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `account_id` int(11) unsigned NOT NULL auto_increment,
  `member_id` int(11) unsigned NOT NULL default '0',
  `userid` varchar(23) NOT NULL default '',
  `user_pass` varchar(32) NOT NULL default '',
  `sex` enum('M','F','S') NOT NULL default 'M',
  `email` varchar(39) NOT NULL default '',
  `level` tinyint(3) NOT NULL default '0',
  `state` int(11) unsigned NOT NULL default '0',
  `unban_time` int(11) unsigned NOT NULL default '0',
  `expiration_time` int(11) unsigned NOT NULL default '0',
  `logincount` mediumint(9) unsigned NOT NULL default '0',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_ip` varchar(100) NOT NULL default '',
  `birthdate` date NOT NULL default '0000-00-00',
  `error_message` smallint(5) unsigned NOT NULL default '0',
  `memo` smallint(5) unsigned NOT NULL default '0',
  `ipallow` varchar(20) NOT NULL default '*.*.*.*',
  PRIMARY KEY  (`account_id`),
  KEY `name` (`userid`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2000000 ;

--
-- Dumping data for table `login`
--
-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `send_name` varchar(30) NOT NULL default '',
  `send_id` int(11) unsigned NOT NULL default '0',
  `dest_name` varchar(30) NOT NULL default '',
  `dest_id` int(11) unsigned NOT NULL default '0',
  `title` varchar(45) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `time` int(11) unsigned NOT NULL default '0',
  `status` tinyint(2) NOT NULL default '0',
  `zeny` int(11) unsigned NOT NULL default '0',
  `nameid` int(11) unsigned NOT NULL default '0',
  `amount` int(11) unsigned NOT NULL default '0',
  `refine` tinyint(3) unsigned NOT NULL default '0',
  `attribute` tinyint(4) unsigned NOT NULL default '0',
  `identify` smallint(6) NOT NULL default '0',
  `card0` smallint(11) NOT NULL default '0',
  `card1` smallint(11) NOT NULL default '0',
  `card2` smallint(11) NOT NULL default '0',
  `card3` smallint(11) NOT NULL default '0',
  `serial` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mail`
--
-- --------------------------------------------------------

--
-- Table structure for table `mapreg`
--

CREATE TABLE IF NOT EXISTS `mapreg` (
  `varname` varchar(32) NOT NULL,
  `index` int(11) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL,
  KEY `varname` (`varname`),
  KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mapreg`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

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

--
-- Dumping data for table `members`
--

-- --------------------------------------------------------

--
-- Table structure for table `member_storage`
--

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

--
-- Dumping data for table `member_storage`
--


-- --------------------------------------------------------

--
-- Table structure for table `memo`
--

CREATE TABLE IF NOT EXISTS `memo` (
  `memo_id` int(11) unsigned NOT NULL auto_increment,
  `char_id` int(11) unsigned NOT NULL default '0',
  `map` varchar(11) NOT NULL default '',
  `x` smallint(4) unsigned NOT NULL default '0',
  `y` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`memo_id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `memo`
--


-- --------------------------------------------------------

--
-- Table structure for table `mercenary`
--

CREATE TABLE IF NOT EXISTS `mercenary` (
  `mer_id` int(11) unsigned NOT NULL auto_increment,
  `char_id` int(11) NOT NULL,
  `class` mediumint(9) unsigned NOT NULL default '0',
  `hp` int(12) NOT NULL default '1',
  `sp` int(12) NOT NULL default '1',
  `kill_counter` int(11) NOT NULL,
  `life_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`mer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mercenary`
--


-- --------------------------------------------------------

--
-- Table structure for table `mercenary_owner`
--

CREATE TABLE IF NOT EXISTS `mercenary_owner` (
  `char_id` int(11) NOT NULL,
  `merc_id` int(11) NOT NULL default '0',
  `arch_calls` int(11) NOT NULL default '0',
  `arch_faith` int(11) NOT NULL default '0',
  `spear_calls` int(11) NOT NULL default '0',
  `spear_faith` int(11) NOT NULL default '0',
  `sword_calls` int(11) NOT NULL default '0',
  `sword_faith` int(11) NOT NULL default '0',
  PRIMARY KEY  (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mercenary_owner`
--


-- --------------------------------------------------------

--
-- Table structure for table `party`
--

CREATE TABLE IF NOT EXISTS `party` (
  `party_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(24) NOT NULL default '',
  `exp` tinyint(11) unsigned NOT NULL default '0',
  `item` tinyint(11) unsigned NOT NULL default '0',
  `leader_id` int(11) unsigned NOT NULL default '0',
  `leader_char` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`party_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `party`
--
-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE IF NOT EXISTS `pet` (
  `pet_id` int(11) unsigned NOT NULL auto_increment,
  `class` mediumint(9) unsigned NOT NULL default '0',
  `name` varchar(24) NOT NULL default '',
  `account_id` int(11) unsigned NOT NULL default '0',
  `char_id` int(11) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `egg_id` smallint(11) unsigned NOT NULL default '0',
  `equip` mediumint(8) unsigned NOT NULL default '0',
  `intimate` smallint(9) unsigned NOT NULL default '0',
  `hungry` smallint(9) unsigned NOT NULL default '0',
  `rename_flag` tinyint(4) unsigned NOT NULL default '0',
  `incuvate` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pet`
--


-- --------------------------------------------------------

--
-- Table structure for table `quest`
--

CREATE TABLE IF NOT EXISTS `quest` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `quest_id` int(10) unsigned NOT NULL,
  `state` enum('0','1','2') NOT NULL default '0',
  `time` int(11) unsigned NOT NULL default '0',
  `count1` mediumint(8) unsigned NOT NULL default '0',
  `count2` mediumint(8) unsigned NOT NULL default '0',
  `count3` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quest`
--

-- --------------------------------------------------------

--
-- Table structure for table `ragsrvinfo`
--

CREATE TABLE IF NOT EXISTS `ragsrvinfo` (
  `index` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `exp` int(11) unsigned NOT NULL default '0',
  `jexp` int(11) unsigned NOT NULL default '0',
  `drop` int(11) unsigned NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ragsrvinfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `sc_data`
--

CREATE TABLE IF NOT EXISTS `sc_data` (
  `account_id` int(11) unsigned NOT NULL,
  `char_id` int(11) unsigned NOT NULL,
  `type` smallint(11) unsigned NOT NULL,
  `tick` int(11) NOT NULL,
  `val1` int(11) NOT NULL default '0',
  `val2` int(11) NOT NULL default '0',
  `val3` int(11) NOT NULL default '0',
  `val4` int(11) NOT NULL default '0',
  KEY `account_id` (`account_id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sc_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE IF NOT EXISTS `skill` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `lv` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `skill`
--

-- --------------------------------------------------------

--
-- Table structure for table `skill_homunculus`
--

CREATE TABLE IF NOT EXISTS `skill_homunculus` (
  `homun_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `lv` smallint(6) NOT NULL,
  PRIMARY KEY  (`homun_id`,`id`),
  KEY `homun_id` (`homun_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `skill_homunculus`
--


-- --------------------------------------------------------

--
-- Table structure for table `sstatus`
--

CREATE TABLE IF NOT EXISTS `sstatus` (
  `index` tinyint(4) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `user` int(11) unsigned NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sstatus`
--


-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE IF NOT EXISTS `storage` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `account_id` int(11) unsigned NOT NULL default '0',
  `nameid` int(11) unsigned NOT NULL default '0',
  `amount` smallint(11) unsigned NOT NULL default '0',
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
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Dumping data for table `storage`
--

-- --------------------------------------------------------

--
-- Table structure for table `woe_skillcount`
--

CREATE TABLE IF NOT EXISTS `woe_skillcount` (
  `char_id` int(11) unsigned NOT NULL default '0',
  `id` smallint(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`char_id`,`id`),
  KEY `char_id` (`char_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `woe_skillcount`
--


-- --------------------------------------------------------

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