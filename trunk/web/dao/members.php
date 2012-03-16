<?php
	namespace dao
	{
		class members extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'aim':
						$max = 15;
						break;
					case 'banned':
						$value = (bool)$value;
						break;
					case 'country':
						$max = 20;
						break;
					case 'email':
					case 'msn':
					case 'ref_email':
						$max = 39;
						break;
					case 'last_ip':
						$max = 20;
						break;
					case 'last_login':
						$max = \lib\timer\current_timestamp();
						break;
					case 'mlevel':
					case 'sex':
						$value = (bool)$value;
						break;
					case 'name':
						$max = 50;
						break;
					case 'services':
					case 'ref_points':
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'ref_option':
						$max = \config\constants\number::UTINT_MAX;
						break;
				}


				if ( $this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __get($name)
			{
				switch ($name)
				{
					case 'mpass':
						return '';
				}

				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'members';
				$this->id_column = 'member_id';

				parent::__construct();

				$this->data['aim'] = '';
				$this->data['banned'] = 0;
				$this->data['country'] = '';
				$this->data['email'] = '';
				$this->data['last_ip'] = '';
				$this->data['last_login'] = 0;
				$this->data['mlevel'] = 0;
				$this->data['mpass'] = '';
				$this->data['msn'] = '';
				$this->data['name'] = '';
				$this->data['ref_email'] = '';
				$this->data['ref_option'] = 0;
				$this->data['ref_points'] = 0;
				$this->data['services'] = '';
				$this->data['sex'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
