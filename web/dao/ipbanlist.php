<?php
	namespace dao
	{
		class ipbanlist extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'reason':
						$max = 255;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __construct($ip = null)
			{
				$this->table = 'ipbanlist';
				$this->id_column = 'list';

				parent::__construct();

				$this->data[$this->id_column] = '';
				$this->data['btime'] = 0; //ban time
				$this->data['reason'] = '';
				$this->data['rtime'] = 0; //expire time

				db::log_error('Attempting to use ipbanlist which needs completed!!', true);

				if ( !is_null($ip) )
				{
						//should be a well-formed ipv4 addr
						//"SELECT count(*) FROM `ipbanlist` WHERE `rtime` > NOW() AND (`list` = '%u.*.*.*' OR `list` = '%u.%u.*.*' OR `list` = '%u.%u.%u.*' OR `list` = '%u.%u.%u.%u')",
						//	ipban_table, p[3], p[3], p[2], p[3], p[2], p[1], p[3], p[2], p[1], p[0]) )
				}

			}
		}
	}