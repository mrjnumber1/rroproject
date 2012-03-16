<?php
	namespace dao
	{
		class sc_data extends db
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
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'type':
						$max = \config\constants\ragnarok\status_change::SC_MAX;
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
						if ( $this->data[$name] )
						{
							return new login($this->data[$name]);
						}
					case 'char_id':
						if ( $this->data[$name] )
						{
							return new char($this->data[$name]);
						}
				}

				parent::__get($name);
			}

			public function __construct($id = null, $char = true)
			{
				$this->table = 'sc_data';

				if ($char)
				{
					$this->id_column = 'char';
					$this->data['account_id'] = 0;
				}
				else
				{
					$this->id_column = 'account_id';
					$this->data['char'] = 0;
				}

				$this->data['tick'] = 0;
				$this->data['type'] = 0;
				$this->data['val1'] = 0;
				$this->data['val2'] = 0;
				$this->data['val3'] = 0;
				$this->data['val4'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}

		}
	}