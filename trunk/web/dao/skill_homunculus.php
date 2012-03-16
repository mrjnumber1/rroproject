<?php
	namespace dao
	{
		class skill_homunculus extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'id':
						$min = \config\constants\ragnarok\homunculus::HM_SKILLBASE;
						$max = $min + \config\constants\ragnarok\homunculus::MAX_HOMUNSKILL;
						break;
					case 'lv':
						// TODO: add real max lv checker :)
						$max = 10;
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
					case 'homun_id':
						return new homunculus($this->data[$name]);
				}

				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'skill_homunculus';
				$this->id_column = 'homun_id';

				parent::__construct();

				$this->data['id'] = 0;
				$this->data['lv'] = 0;


				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}

		}
	}