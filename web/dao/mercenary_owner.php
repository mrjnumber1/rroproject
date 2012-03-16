<?php
	namespace dao
	{
		class mercenary_owner extends db
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
					case 'char_id':
						return new char($this->data['char_id']);
				}

				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'mercenary_owner';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['mer_id'] = 0;
				$this->data['arch_calls'] = 0;
				$this->data['arch_faith'] = 0;
				$this->data['spear_calls'] = 0;
				$this->data['spear_faith'] = 0;
				$this->data['sword_calls'] = 0;
				$this->data['sword_faith'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}
		}
	}