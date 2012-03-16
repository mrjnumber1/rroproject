<?php

	namespace dao
	{
		class interlog extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = 255;

				switch ($name)
				{
					case 'time':
						$max = \lib\timer\current_timestamp();
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __get($name)
			{
				switch ($name)
				{
					case 'time':
						return new \DateTime($this->data[$name]);
						break;
				}

				return parent::__get($name);
			}

			// TODO: no id column oh god
			public function __construct()
			{
				$this->table = 'interlog';

				$this->data['log'] = '';
				$this->data['time'] = 0;
			}
		}
	}