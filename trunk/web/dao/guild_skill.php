<?php
	namespace dao
	{
		class guild_skill extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'guild_id':
						$max = \config\constants\number::SINT_MAX;
						break;
					case 'id':
						$min = \config\constants\ragnarok\guild::GD_SKILLBASE;
						$max = $min + \config\constants\ragnarok\guild::MAX_GUILDSKILL;
						break;
					case 'lv':
						//TODO: HAVE THIS APPLY BASED ON ID IF SET.
						$max = 10;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'guild_skill';
				$this->id_column = 'guild_id';

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
