<?php
	namespace dao
	{
		class login extends db
		{

			public function __get($name)
			{
				switch($name)
				{

				}
				return parent::__get($name);
			}

			public function __set($name, $value)
			{
				$min = 0;
				$max = 0xFFFFFF;
				switch($name)
				{
					case 'account_id':
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						break;
					case 'member_id':
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
				}

				parent::__set($name, $value);
				return;
			}
			public function __construct($id = null)
			{
				$this->id_column = 'account_id';
				$this->table = 'login';

				parent::__construct();

				$this->data[$this->id_column] = null;
				$this->data['member_id'] = null;
				$this->data['userid'] = null;
				$this->data['user_pass'] = '';
				$this->data['sex'] = null;
				$this->data['email'] = '';
				$this->data['level'] = 0;
				$this->data['state'] = null;
				$this->data['unban_time'] = 0;
				$this->data['expiration_time'] = 0;
				$this->data['logincount'] = 0;
				$this->data['lastlogin'] = null;
				$this->data['last_ip'] = null;
				$this->data['birthdate'] = null;
				$this->data['error_message'] = null;
				$this->data['memo'] = null;
				$this->data['ipallow'] = '';

				if ( !is_null($id) )
				{
					$this->load_by_id($id);
				}
			}
		}
	}