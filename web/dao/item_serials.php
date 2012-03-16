<?php
	namespace dao
	{
		class item_serials extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX;

				switch ($name)
				{
					case 'nameid':
						$min = \config\constants\ragnarok\item::START_ITEMID;
						$max = \config\constants\ragnarok\item::MAX_ITEMID;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __get($name)
			{
				switch($name)
				{
					case 'nameid':
						return new item_db($this->data[$name]);
						break;
				}

				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'item_serials';
				$this->id_column = 'nameid';

				parent::__construct();

				$this->data['serial'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}