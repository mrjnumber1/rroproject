<?php

	namespace config 
	{
		class config
		{
				public static $db_dsn = 'mysql:host=HOSTNAMEHERE;dbname=DBNAMEHERE';
				public static $db_user = 'USERNAMEHERE';
				public static $db_pass = 'PASSWORDHERE';
				public static $db_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');			
				public static $ro_ip = array('HOST', 'HOST', 'HOST');
				public static $ro_port = array(######, ####, ####);
		}

	}
