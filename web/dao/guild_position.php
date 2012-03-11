<?php
	namespace dao
	{
		class guild_position extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'position':
						$max = \config\constants\ragnarok\guild::MAX_GUILD_POSITION;
						break;
					case 'mode':
						$max = \config\constants\ragnarok\guild::MODE_EXPEL
							 | \config\constants\ragnarok\guild::MODE_INVITE;
						break;
					case 'exp_mode':
						$max = \config\constants\ragnarok\guild::MAX_TAX;
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'guild_position';
				$this->id_column = 'guild_id';

				parent::__construct();

				$this->data['position'] = 0;
				$this->data['mode']     = 0;
				$this->data['exp_mode'] = 0;
				$this->data['name']     = '';

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}