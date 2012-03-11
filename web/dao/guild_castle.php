<?php
	namespace dao
	{
		class guild_castle extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'defense':
						$max = \config\constants\ragnarok\guild::MAX_DEFENSE;
						break;
					case 'economy':
						$max = \config\constants\ragnarok\guild::MAX_ECONOMY;
						break;
					case 'triggerD':
					case 'triggerE':
						$max = \config\constants\ragnarok\guild::MAX_INVESTS_PER_DAY;
						break;
					case 'visibleC': // kafra
					case 'visibleG0':
					case 'visibleG1':
					case 'visibleG2':
					case 'visibleG3':
					case 'visibleG4':
					case 'visibleG5':
					case 'visibleG6':
					case 'visibleG7':
						$value = (bool)$value;
						break;
					case 'createTime':
					case 'nextTime':
					case 'payTime':
						$max = \config\constants\number::UINT_MAX;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __construct($id = null)
			{
				$this->table = 'guild_castle';
				$this->id_column = 'castle_id';

				parent::__construct();

				$this->data['createTime'] = 0;
				$this->data['defense'] = 0;
				$this->data['economy'] = 0;
				$this->data['guild_id'] = 0;
				$this->data['nextTime'] = 0;
				$this->data['payTime'] = 0;
				$this->data['triggerD'] = 0;
				$this->data['triggerE'] = 0;
				$this->data['visibleC'] = false;
				$this->data['visibleG0'] = false;
				$this->data['visibleG1'] = false;
				$this->data['visibleG2'] = false;
				$this->data['visibleG3'] = false;
				$this->data['visibleG4'] = false;
				$this->data['visibleG5'] = false;
				$this->data['visibleG6'] = false;
				$this->data['visibleG7'] = false;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
