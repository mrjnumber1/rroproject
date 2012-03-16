<?php
	namespace dao
	{
		class mob_db extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'ID':
						$min = \config\constants\ragnarok\mob_db::MOB_DB_START;
						$max = \config\constants\ragnarok\mob_db::MOB_CLONE_START - 1;
						break;
					case 'iName':
					case 'kName':
					case 'Sprite':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'LV':
						$min = 1;
						$max = 150;
						break;
					case 'HP':
						$min = 1;
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'SP':
						$min = 1;
						$max = \config\constants\number::UMINT_MAX;
						break;
					case 'Range1':
					case 'Range2':
					case 'Range3':
						// determined through searching, 16 is the biggest range3 we see from mob types
						$max = 16;
						break;
					case 'Scale':
						//$max = \config\constants\ragnarok\enums\size::max_value();
						break;
					case 'Element':
						// TODO : give meaning to these constants!!
						// TODO : bound check this so we have no invalids (0-19/30-39 are invalid for example)
						// Ele = EleLevel*20 + DefEle
						$min = 20; // Neutral 1
						$max = 89; // Dark 4
						break;
					case 'Race':
						//$min = \config\constants\ragnarok\enums\race::min_value();
						//$max = \config\constants\ragnarok\enums\race::Dragon;
						break;
					case 'Mode':
						$max = \config\constants\ragnarok\mob_db::MD_MASK;
						break;
					case 'Speed':
					case 'aMotion':
					case 'aDelay':
					case 'dMotion':
					case 'ATK1':
					case 'ATK2':
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'MDEF':
					case 'DEF':
						$max = 100;
						break;
					case 'STR':
					case 'DEX':
					case 'LUK':
					case 'INT':
					case 'VIT':
						$min = 1;
						$max = \config\constants\number::UTINT_MAX;
						break;
					case 'MEXP':
					case 'EXP':
					case 'JEXP':
						$max = \config\constants\number::UMINT_MAX;
						break;
					case 'Drop1per':
					case 'Drop2per':
					case 'Drop3per':
					case 'Drop4per':
					case 'Drop5per':
					case 'Drop6per':
					case 'Drop7per':
					case 'Drop8per':
					case 'Drop9per':
					case 'DropCardper':
					case 'MVP1per':
					case 'MVP2per':
					case 'MVP3per':
					case 'ExpPer':
						$max = 100*10;
						break;
					case 'Drop1id':
					case 'Drop2id':
					case 'Drop3id':
					case 'Drop4id':
					case 'Drop5id':
					case 'Drop6id':
					case 'Drop7id':
					case 'Drop8id':
					case 'Drop9id':
						if ($value > 0)
						{
							$min = \config\constants\ragnarok\item::START_ITEMID;
						}

						$max = \config\constants\ragnarok\item::MAX_ITEMID;
						break;
					case 'DropCardid':
						if ($value > 0)
						{
							$min = \config\constants\ragnarok\item::START_ITEMID_CARD;
						}

						$max = \config\constants\ragnarok\item::MAX_ITEMID_CARD;
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
					case 'Drop1id':
					case 'Drop2id':
					case 'Drop3id':
					case 'Drop4id':
					case 'Drop5id':
					case 'Drop6id':
					case 'Drop7id':
					case 'Drop8id':
					case 'Drop9id':
					case 'MVP1id':
					case 'MVP2id':
					case 'MVP3id':
					case 'Drop1id':
					case 'Drop1id':
						return new item_db($this->data[$name]);
					case 'ID':
						return $this;
					case 'EXP':
						if ($this->data['MEXP'] > 0)
						{
							return $this->data[$name]
								* \config\battle_config::base_exp_rate
								* \config\battle_config::mvp_exp_rate;
						}
						else
						{
							return $this->data[$name]
								* \config\battle_config::base_exp_rate;
						}
					case 'JEXP':
						return $this->data[$name]
							* \config\battle_config::job_exp_rate;
					case 'MEXP':
						return $this->data[$name]
							* \config\battle_config::mvp_exp_rate;
					case 'Drop1per':
					case 'Drop2per':
					case 'Drop3per':
					case 'Drop4per':
					case 'Drop5per':
					case 'Drop6per':
					case 'Drop7per':
					case 'Drop8per':
					case 'Drop9per':
					case 'DropCardper':
					case 'MVP1per':
					case 'MVP2per':
					case 'MVP3per':
						if ($this->data[$name] == 0)
						{
							return 0;
						}
						else
						{
							if ($name[0] == 'M') // MVP Drop
							{
								return $this->data[$name] * \config\battle_config::item_rate_mvp;
							}

							$nameid_key = \lib\string\left($name, strlen($name) - 3) . 'id';
							$nameid = $this->data[$nameid_key];
							$base_drop_rate = $this->data[$name];
							$is_boss = $this->data['Mode'] & \config\constants\ragnarok\mob_db::MD_BOSS;
							$drop_rate = 0;
							if ($nameid == 0)
							{
								return 0;
							}
							else
							{
								//id as in item_data!!
								$id = new item_db($nameid);

								switch($id->type)
								{
									case \config\constants\ragnarok\enums\item_type::Healing:
										$drop_rate = $base_drop_rate
											  * ($is_boss
											  ? \config\battle_config::item_rate_heal_boss
											  : \config\battle_config::item_rate_heal );
										break;
									case \config\constants\ragnarok\enums\item_type::Usable:
									case \config\constants\ragnarok\enums\item_type::Cash:
										$drop_rate = $base_drop_rate
											* ($is_boss
											? \config\battle_config::item_rate_use_boss
											: \config\battle_config::item_rate_use);
										break;
									case \config\constants\ragnarok\enums\item_type::Weapon:
									case \config\constants\ragnarok\enums\item_type::Armor:
									case \config\constants\ragnarok\enums\item_type::PetArmor:
										$drop_rate = $base_drop_rate
											* ($is_boss
											? \config\battle_config::item_rate_equip_boss
											: \config\battle_config::item_rate_equip);
										break;
									case \config\constants\ragnarok\enums\item_type::Card:
										$drop_rate = $base_drop_rate
											* ($is_boss
											? \config\battle_config::item_rate_card_boss
											: \config\battle_config::item_rate_card);
										break;
									default:
										$drop_rate = $base_drop_rate
											* ($is_boss
											? \config\battle_config::item_rate_common_boss
											: \config\battle_config::item_rate_common);
										break;
								}

								return \lib\string\percent(min(100, $drop_rate/100), false);
							}

						}

						break;

				}

				return parent::__get($name);
			}
			public function __construct($id = null, $use2 = false)
			{
				$this->table = 'mob_db';
				if ($use2)
				{
					$this->table .= '2';
				}
				$this->id_column = 'ID';

				parent::__construct();

				$this->data['Sprite'] = '';
				$this->data['kName'] = '';
				$this->data['iName'] = '';
				$this->data['LV'] = 0;
				$this->data['HP'] = 0;
				$this->data['SP'] = 0;
				$this->data['EXP'] = 0;
				$this->data['JEXP'] = 0;
				$this->data['Range1'] = 0;
				$this->data['ATK1'] = 0;
				$this->data['ATK2'] = 0;
				$this->data['DEF'] = 0;
				$this->data['MDEF'] = 0;
				$this->data['STR'] = 0;
				$this->data['AGI'] = 0;
				$this->data['VIT'] = 0;
				$this->data['INT'] = 0;
				$this->data['DEX'] = 0;
				$this->data['LUK'] = 0;
				$this->data['Range2'] = 0;
				$this->data['Range3'] = 0;
				$this->data['Scale'] = 0;
				$this->data['Race'] = 0;
				$this->data['Element'] = 0;
				$this->data['Mode'] = 0;
				$this->data['Speed'] = 0;
				$this->data['aDelay'] = 0;
				$this->data['aMotion'] = 0;
				$this->data['dMotion'] = 0;
				$this->data['MEXP'] = 0;
				$this->data['ExpPer'] = 0;
				$this->data['MVP1id'] = 0;
				$this->data['MVP1per'] = 0;
				$this->data['MVP2id'] = 0;
				$this->data['MVP2per'] = 0;
				$this->data['MVP3id'] = 0;
				$this->data['MVP3per'] = 0;
				$this->data['Drop1id'] = 0;
				$this->data['Drop1per'] = 0;
				$this->data['Drop2id'] = 0;
				$this->data['Drop2per'] = 0;
				$this->data['Drop3id'] = 0;
				$this->data['Drop3per'] = 0;
				$this->data['Drop4id'] = 0;
				$this->data['Drop4per'] = 0;
				$this->data['Drop5id'] = 0;
				$this->data['Drop5per'] = 0;
				$this->data['Drop6id'] = 0;
				$this->data['Drop6per'] = 0;
				$this->data['Drop7id'] = 0;
				$this->data['Drop7per'] = 0;
				$this->data['Drop8id'] = 0;
				$this->data['Drop8per'] = 0;
				$this->data['Drop9id'] = 0;
				$this->data['Drop9per'] = 0;
				$this->data['DropCardid'] = 0;
				$this->data['DropCardper'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}

		}
	}