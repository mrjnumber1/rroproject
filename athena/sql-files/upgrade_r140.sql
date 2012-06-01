CREATE TABLE IF NOT EXISTS `zeny` (
  `account_id` int(11) unsigned NOT NULL default '0',
  `amount` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`account_id`)
) ENGINE=MyISAM;

REPLACE INTO `zeny`(`account_id`,`amount`)
 SELECT `account_id`, SUM(`zeny`)
 FROM   `char`
 GROUP BY `account_id`;

ALTER TABLE `char` DROP `zeny`;

