<?php
	namespace dao
	{
		class hotkey extends db
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
					case 'hotkey':
						$max = \config\constants\ragnarok\hotkey::MAX_HOTKEYS;
						break;
					case 'skill_lvl':
						if ( $this->data['type'] == \config\constants\ragnarok\hotkey::TYPE_ITEM )
						{
							$max = $min = 0;
						}
						else
						{
							//TODO: a real maximum skill level<->id finder here!
							$max = 10;
						}
						break;
					case 'type':
						$min = \config\constants\ragnarok\hotkey::TYPE_ITEM;
						$max = \config\constants\ragnarok\hotkey::TYPE_SKILL;
						break;
					case 'itemskill_id':
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
						return new char($this->data[$name]);
						break;
					case 'itemskill_id':
						if ( $this->data['type'] == \config\constants\ragnarok\hotkey::TYPE_ITEM )
						{
							return new item_db($this->data['itemskill_id']);
						}
						elseif ( $this->data['type'] == \config\constants\ragnarok\hotkey::TYPE_SKILL )
						{
							return intval($this->data['itemskill_id']);
						}
						else
						{
							db::log_error('Invalid Type received for hotkey', true);
						}
					case 'skill_lvl':
						if ( $this->data['type'] != \config\constants\ragnarok\hotkey::TYPE_SKILL )
						{
							db::log_error('Forced to set skill_lvl to 0 for Non-Skill Hotkey: ' . $this->data['type'], false);
							$this->data['skill_lvl'] == 0;
						}
						break;
				}
				return parent::__get($name);
			}

			public function __construct($id = null)
			{
				$this->table = 'hotkey';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->hotkey = 0;
				$this->itemskill_id = 0;
				$this->skill_lvl = 0;
				$this->type = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}

	}