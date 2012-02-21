<?php

	namespace config 
	{
		$fp = fopen('./login.txt')
		$login = array();

		$login['dsn'] = fgets($fp);
		$login['user'] = fgets($fp);
		$login['pass'] = fgets($fp);

		fclose($fp);

		public class config 
		{
				public static $db_dsn = $login['dsn'];
				public static $db_user = $login['user'];
				public static $login['pass'];
				public static $db_options array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8';
		}

		unset($login);
	}
