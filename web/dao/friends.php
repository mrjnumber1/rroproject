<?php
	namespace dao
	{
		class friends extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'friend_id':
						if ( intval($value) == $this->data['char_id'] )
						{
							db::log_error('Friend ID should never match Char ID!!!', true);
							return;
						}
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'friend_account':
						// TODO: make sure this is never the same account as char_id
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
				}

				if( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'friends';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['friend_account'] = 0;
				$this->data['friend_id']      = 0;

				if ( !is_null($id))
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}