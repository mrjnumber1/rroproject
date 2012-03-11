<?php
	namespace dao
	{
		class guild_member extends db
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
					case 'account_id':
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
					case 'guild_id':
						$max = \config\constants\number::SINT_MAX;
						break;
					case 'class':
						// TODO: add a real job id checker of some sort..
						$max = 4049;
						break;
					case 'gender':
						// 0 = female
						// 1 = male
					case 'online':
						$value = (bool)$value;
						break;
					case 'position':
						$max = \config\constants\ragnarok\guild::MAX_GUILD_POSITION;
						break;
					case 'hair':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_HAIR_STYLE;
						break;
					case 'hair_color':
						$max = \config\constants\ragnarok\character::MAX_HAIR_COLOR;
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'exp_payper':
						// in 6 years of using athena... this has ALWAYS been 0.
						$max = 0;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'guild_member';
				$this->id_column = 'guild_id';

				parent::__construct();

				$this->data['char_id'] = 0;
				$this->data['account_id'] = 0;
				$this->data['class'] = 0;
				$this->data['exp'] = 0;
				$this->data['exp_payper'] = 0;
				$this->data['gender'] = 0;
				$this->data['hair'] = 0;
				$this->data['hair_color'] = 0;
				$this->data['lv'] = 0;
				$this->data['name'] = '';
				$this->data['online'] = 0;
				$this->data['position'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
