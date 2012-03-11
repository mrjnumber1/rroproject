<?php
	namespace dao
	{
		class guild_rank extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX; //different from the others!!

				switch ($name)
				{
					case 'guild_id':
						$max = \config\constants\number::SINT_MAX;
						break;
					case 'castle_id':
						$max = \config\constants\ragnarok\guild::MAX_GUILDCASTLE;
						break;
				}

				if( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			public function __construct($id = null)
			{
				$this->table = 'guild_rank';
				$this->id_column = 'guild_id';

				parent::__construct();

				$this->data['castle_id'] = 0;
				$this->data['capture'] = 0;
				$this->data['emperium'] = 0;
				$this->data['treasure'] = 0;
				$this->data['top_eco'] = 0;
				$this->data['top_def'] = 0;
				$this->data['invest_eco'] = 0;
				$this->data['invest_def'] = 0;
				$this->data['offensive_score'] = 0;
				$this->data['defensive_score'] = 0;
				$this->data['possession_time'] = 0;
				$this->data['zeny_eco'] = 0;
				$this->data['zeny_def'] = 0;
				$this->data['skill_battleorder'] = 0;
				$this->data['skill_regeneration'] = 0;
				$this->data['skill_restore'] = 0;
				$this->data['skill_emergencycall'] = 0;
				$this->data['off_kill'] = 0;
				$this->data['off_death'] = 0;
				$this->data['def_kill'] = 0;
				$this->data['def_death'] = 0;
				$this->data['ext_kill'] = 0;
				$this->data['ext_death'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
