<?php
	namespace dao
	{
		class guild_alliance extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'alliance_id':
						if (intval($value) == $this->data['guild_id'])
						{
							db::log_error('Cannot set alliance guild id equal to guild id!', true);
							return;
						}
						break;
					case 'guild_id':
						if (intval($value) == $this->data['alliance_id'])
						{
							db::log_error('Cannot set guild id equal to alliance guild id!', true);
							return;
						}
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'opposition':
						$value = (bool)$value;
						break;

				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __construct($id = null)
			{
				$this->table = 'guild_alliance';
				$this->id_column = 'guild_id';

				parent::__construct();

				$this->data['alliance_id']   = 0;
				$this->data['name']       = '';
				$this->data['opposition'] = false;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
