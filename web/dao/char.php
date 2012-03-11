<?php
	namespace dao
	{
		class char extends db
		{
			public function __set($name, $value)
			{
				$max = 0xFFFFFFFF;
				$min = 0;
				
				switch ($name)
				{
					case 'account_id':
						$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
						$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
						break;
					case 'partner_id':
					case 'mother':
					case 'father':
						if (intval($value) == $this->data['char_id'])
						{
							db::log_error('Value '.$name.'cannot equal the current char_id! via __set()', true);
							return;
						}
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'guild_id':
						$max = \config\constants\number::SINT_MAX;
						break;
					case 'char_num':
						$max = \config\constants\ragnarok\character::MAX_CHARS;
						break;
					case 'class':
						//TODO: add a real job id checker of some sort..
						$max = 4049;
						break;
					case 'base_level':
						$max = \config\constants\ragnarok\character::MAX_LEVEL;
						break;
					case 'job_level':
						$max = 99; // TODO: add a real checker here based on $this->data['class']
						break;
					case 'hair':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_HAIR_STYLE;
						break;
					case 'hair_color':
						$max = \config\constants\ragnarok\character::MAX_HAIR_COLOR;
						break;
					case 'clothes_color':
						$max = \config\constants\ragnarok\character::MAX_CLOTH_COLOR;
						break;
					case 'str':
					case 'agi':
					case 'int':
					case 'dex':
					case 'luk':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_STAT;
						break;
					case 'weapon':
					//	$max = \config\constants\ragnarok\enums\weapon_type::max_value(); //TODO: add these functions
					//	$min = \config\constants\ragnarok\enums\weapon_type::min_value();
						break;
					case 'shield':
					//	$max = \config\constants\ragnarok\enums\shield_type::max_value();
						break;
					case 'last_map':
					case 'save_map':
						$min = \config\constants\ragnarok\map::MAP_NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\map::MAP_NAME_LENGTH;
						break;
					case 'last_x':
					case 'last_y':
					case 'save_y':
					case 'save_x':
						$min = 50;
						$max = 400;
						break;
					case 'fame':
						$max = \config\constants\ragnarok\character::MAX_FAME;
						break;
					case 'delete_date':
						$min = -1;
						$max = \lib\timer\current_timestamp();
					case 'online':
						$value = (bool)$value;
				}
				
				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}
			
			public function __construct($id = null)
			{
				$this->table     = 'char';
				$this->id_column = 'char_id';
				
				parent::__construct();
				
				$this->data['account_id']   = null;
				$this->data['char_num']     = 0;
				$this->data['name']         = '';
				$this->data['class']        = 0;
				$this->data['base_level']   = 1;
				$this->data['job_level']    = 1;
				$this->data['base_exp']     = 1;
				$this->data['job_exp']      = 1;
				$this->data['zeny']         = 0;
				$this->data['str']          = 1;
				$this->data['agi']          = 1;
				$this->data['vit']          = 1;
				$this->data['int']          = 1;
				$this->data['dex']          = 1;
				$this->data['luk']          = 1;
				$this->data['max_hp']       = 40;
				$this->data['hp']           = 40;
				$this->data['max_sp']       = 11;
				$this->data['sp']           = 11;
				$this->data['status_point'] = 48;
				$this->data['skill_point']  = 0;
				$this->data['option']       = 0;
				$this->data['karma']        = 0;
				$this->data['manner']       = 0;
				$this->data['party_id']     = 0;
				$this->data['guild_id']     = 0;
				$this->data['pet_id']       = 0;
				$this->data['homun_id']     = 0;
				$this->data['hair']         = 1;
				$this->data['hair_color']   = 0;
				$this->data['clothes_color']= 0;
				$this->data['weapon']       = 0;
				$this->data['shield']       = 0;
				$this->data['head_top']     = 0;
				$this->data['head_mid']     = 0;
				$this->data['head_bottom']  = 0;
				$this->data['robe']         = 0;
				$this->data['last_map']     = 'new_1-1';
				$this->data['last_x']       =  53;
				$this->data['last_y']       = 111;
				$this->data['save_map']     = 'new_1-1'; //TODO: add start map config here for last_map and save_map on the ctor
				$this->data['save_x']       =  53;
				$this->data['save_y']       = 111;
				$this->data['partner_id']   = 0;
				$this->data['online']       = false;
				$this->data['father']       = 0;
				$this->data['mother']       = 0;
				$this->data['child']        = 0;
				$this->data['fame']         = 0;
				$this->data['rename']       = 0;
				$this->data['delete_date']  = 0;
				$this->data['playtime']     = 0;

				$this->data_ext['userid']    = 0;
				$this->data_ext['sex']       = 0;
				$this->data_ext['gm_level']  = 0;
				$this->data_ext['party_name']= 0;
				$this->data_ext['guild_name']= 0;
				$this->data_ext['deaths']    = 0;

                if ( !is_null($id) )
                {
                    $this->load_by_id( intval($id) );
                }
			}
		}
	}