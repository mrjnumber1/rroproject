<?php

	namespace dao
	{
		class mercenary extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'class':
						$min = \config\constants\ragnarok\mercenary::MERC_CLASS_BASE;
						$max = \config\constants\ragnarok\mercenary::MERC_CLASS_MAX;
						break;
					case 'mer_id':
						$max = \config\constants\number::UINT_MAX;
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
					case 'master_id':
					case 'char_id':
						return new char($this->data['char_id']);
					case 'life_time':
						return new \DateTime($this->data[$name]);
				}

				return parent::__get($name);
			}
			public function __construct($id = null)
			{
				$this->id_column = 'char_id';
				$this->table = 'mercenary';

				parent::__construct();

				$this->data['mer_id'] = 0;
				$this->data['class'] = 0;
				$this->data['kill_counter'] = 0;
				$this->data['life_time'] = 0;
				$this->data['hp'] = 0;
				$this->data['sp'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}