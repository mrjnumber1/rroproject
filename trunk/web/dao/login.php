<?php
	namespace dao
	{
		class login extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch($name)
				{
					case 'account_id':
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						break;
					case 'email':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = 39;
						break;
					case 'error_message':
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'ipallow':
						$max = 20;
						break;
					case 'last_ip':
						$max = 100;
						break;
					case 'logincount':
						$max = \config\constants\number::UMINT_MAX;
						break;
					case 'member_id':
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
					case 'sex':
						//$max = \config\constants\ragnarok\enums\sex::Server;
						break;
					case 'userid':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}

				return;
			}


			public function __get($name)
			{
				switch($name)
				{
					case 'member_id':
						return new members($this->data[$name]);
					case 'birthdate':
					case 'lastlogin':
						return new \DateTime($this->data[$name]);
					case 'user_pass':
						return '';
				}
				return parent::__get($name);
			}


			public function __construct($id = null)
			{
				$this->id_column = 'account_id';
				$this->table = 'login';

				parent::__construct();

				$this->data['member_id'] = 0;
				$this->data['userid'] = 0;
				$this->data['user_pass'] = '';
				$this->data['sex'] = 0;
				$this->data['email'] = '';
				$this->data['level'] = 0;
				$this->data['state'] = 0;
				$this->data['unban_time'] = 0;
				$this->data['expiration_time'] = 0;
				$this->data['logincount'] = 0;
				$this->data['lastlogin'] = 0;
				$this->data['last_ip'] = 0;
				$this->data['birthdate'] = 0;
				$this->data['error_message'] = 0;
				$this->data['memo'] = 0;
				$this->data['ipallow'] = '';

				if ( !is_null($id) )
				{
					$this->load_by_id($id);
				}
			}
		}
	}