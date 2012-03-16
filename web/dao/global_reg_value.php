<?php
	namespace dao
	{
		class global_reg_value extends db
		{
			private $id_type = \config\constants\ragnarok\registry::CHAR_ID;

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'str':
						$min = 1; // no 0-length strings!
						$max = 255;
						break;
					case 'type':
						db::log_error('Type is not to be changed!');
						return;
						break;
					case 'char_id':
						if ($this->id_type != \config\constants\ragnarok\registry::CHAR_ID)
						{
							db::log_error('Attempting to set char_id for non-global char reg', true);
							return;
						}

						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'account_id':

						switch ($this->id_type)
						{
							case \config\constants\ragnarok\registry::CHAR_ID:
								db::log_error('Attempting to set account_id for global char reg', true);
								return;
								break;
							case \config\constants\ragnarok\registry::ACCOUNT_ID:
								$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
								$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
								break;
						}

					case 'value':
						if( is_string($value) )
						{
							$min = 1; // no 0-length strings!
							$max = 255;
						}
						else if ( is_int($value) )
						{
							$min = \config\constants\number::INT_MIN;
						}
						else
						{
							db::log_error('Invalid value via __set'.$name, true);
							return;
						}
						break;
				}

				switch ($this->data['str'])
				{
					case 'COOK_MASTERY':
						$max = \config\constants\ragnarok\character::MAX_COOK_MASTERY;
						break;
					case '##CASHPOINTS':
					case '##KAFRAPOINTS':
						$max = \config\constants\ragnarok\character::MAX_CASH;
						break;
					case '##SECURITY':
						//TODO: make this setable by GM logins?
						db::log_error('Attempted to set unsetable value', true);
						break;
				}


				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __get($name)
			{
				if($name == 'value' && $this->data['str'] == '##SECURITY')
				{
					//TODO: make this getable by GM logins
					db::log_error('Attempted to retrieve irretrievable value', true);
					return 0;
				}
				switch ($name)
				{
					case 'value':
						if ( $this->data['str'] == '##SECURITY' )
						{
							db::log_error('Attempted to retrieve irretrievable value', true);
						}
				}

				return parent::__get($name);
			}



			public function __construct($id = null, $id_type = \config\constants\ragnarok\registry::CHAR_ID)
			{
				$this->table = 'global_reg_value';

				$this->id_type = intval($id_type);

				switch($this->id_type)
				{
					case \config\constants\ragnarok\registry::CHAR_ID:
						$this->id_column = 'char_id';
						$this->data['type']  = \config\constants\ragnarok\registry::GLOBAL_REG;
						$this->data['account_id'] = 0;
						break;
					case \config\constants\ragnarok\registry::ACCOUNT_ID:
						$this->id_column = 'account_id';
						$this->data['type']  = \config\constants\ragnarok\registry::ACCOUNT_REG;
						$this->data['char_id'] = 0;
						break;
					case \config\constants\ragnarok\registry::MEMBER_ID:
						$this->id_column = 'account_id';
						$this->data['type']  = \config\constants\ragnarok\registry::MEMBER_REG;
						$this->data['char_id'] = 0;
						break;
					default:
						db::log_error('Invalid Reg Type received!', true);
						break;
				}

				parent::__construct();

				$this->data['str']   = '';;
				$this->data['value'] = '';


				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
