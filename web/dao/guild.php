<?php
	namespace dao
	{
		class guild extends db
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
					case 'average_lv':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_LEVEL;
						break;
					case 'guild_lv':
						$min = 1;
						$max = \config\constants\ragnarok\guild::MAX_GUILDLEVEL;
						break;
					case 'exp':
					case 'next_exp':
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'connect_member':
					case 'max_member':
						//$max = extension_level * \config\constants\ragnarok\guild::MEMBERS_PER_GUILD_EXTENSION
						//	+ \config\constants\ragnarok\guild::DEFAULT_GUILD_MEMBERS;
						$max = \config\constants\ragnarok\guild::MAX_GUILD;
						break;
					case 'name':
					case 'master':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'mes1':
						$max = 60;
						break;
					case 'mes2':
						$max = 120;
						break;
					case 'emblem_len':
						$max = 15*1024; // TODO: figure this out; 15 KiB is guessed.
						break;
					case 'emblem_data':
						$max = 15*1024;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __construct($id = null)
			{
				$this->table = 'guild';
				$this->id_column = 'guild_id';

				parent::__construct();

				$this->data['name']          = '';
				$this->data['char_id']       = 0;
				$this->data['master']        = '';
				$this->data['guild_lv']      = 1;
				$this->data['connect_member']= 0;
				$this->data['max_member']    = 0;
				$this->data['average_lv']    = 0;
				$this->data['exp']           = 0;
				$this->data['next_exp']      = 0;
				$this->data['skill_point']   = 0;
				$this->data['mes1']          = '';
				$this->data['mes2']          = '';
				$this->data['emblem_len']    = 0;
				$this->data['emblem_id']     = 0;
				$this->data['emblem_data']   = null;


				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
