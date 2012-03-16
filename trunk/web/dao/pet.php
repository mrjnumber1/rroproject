<?php
	namespace dao
	{
		class pet extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'account_id':
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'class':
						$min = \config\constants\ragnarok\mob_db::MOB_DB_START;
						$max = \config\constants\ragnarok\mob_db::MOB_CLONE_START-1;
						break;
					case 'equip':
					case 'egg_id':
						$min = \config\constants\ragnarok\item::START_ITEMID;
						$max = \config\constants\ragnarok\item::MAX_ITEMID;
						break;
					case 'hungry':
						$max = \config\constants\ragnarok\homunculus::MAX_HUNGER;
						break;
					case 'intimate':
						$max = \config\constants\ragnarok\homunculus::MAX_INTIMACY;
						break;
					case 'level':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_LEVEL;
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'incuvate':
					case 'rename_flag':
						$value = (bool)$value;
					case 'pet_id':
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
					case 'account_id':
						return new login($this->data[$name]);
					case 'char_id':
						return new char($this->data[$name]);
					case 'class':
						return new mob_db($this->data[$name]);
					case 'equip':
					case 'egg_id':
						return new item_db($this->data[$name]);
				}

				return parent::__get($name);
			}
			public function __construct($id = null)
			{
				$this->table = 'pet';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['pet_id'] = 0;
				$this->data['account_id'] = 0;
				$this->data['class'] = 0;
				$this->data['egg_id'] = 0;
				$this->data['equip'] = 0;
				$this->data['hungry'] = 0;
				$this->data['incuvate'] = 0;
				$this->data['intimate'] = 0;
				$this->data['level'] = 0;
				$this->data['name'] = '';
				$this->data['rename_flag'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}

		}
	}