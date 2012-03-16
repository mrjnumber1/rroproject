<?php
	namespace dao
	{
		class party extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX;

				switch ($name)
				{
					case 'exp':
						// true = EXP share :)
						$value = (bool)$value;
						break;
					case 'item':
						$max = \config\constants\ragnarok\party::OPT_PICKUPSHARE
					 		 | \config\constants\ragnarok\party::OPT_REDISTRIBUTE;
						break;
					case 'leader_char':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'leader_id':
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
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
					case 'leader_char':
						return new char($this->data[$name]);
					case 'leader_id':
						return new login($this->data[$name]);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'party';
				$this->id_column = 'party_id';

				parent::__construct();

				$this->data['exp'] = 0;
				$this->data['item'] = 0;
				$this->data['leader_char'] = 0; // char id
				$this->data['leader_id'] = 0; // account id
				$this->data['name'] = '';

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}

		}
	}