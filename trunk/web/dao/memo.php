<?php

	namespace dao
	{
		class memo extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = 400;

				switch ($name)
				{
					case 'memo_id':
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'map':
						$min = \config\constants\ragnarok\map::MAP_NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\map::MAP_NAME_LENGTH;
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
					case 'char_id':
						return new char($this->data[$name]);
				}

				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'memo';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['memo_id'] = 0;
				$this->data['map'] = '';
				$this->data['y'] = 0;
				$this->data['x'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}