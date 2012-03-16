<?php
	namespace dao
	{
		class skill extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX;

				switch ($name)
				{
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'id':
						$min = 1;
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'lv':
						// TODO: add real max lv checker :)
						$max = 10;
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
				$this->table = 'skill';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['id'] = 0;
				$this->data['lv'] = 0;


				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}

		}
	}