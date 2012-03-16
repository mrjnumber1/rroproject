<?php

	namespace dao
	{
		class bg_log extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'killer_id':
					case 'killed_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'skill':
						// 0 = Melee/Reflect
						$max = \config\constants\ragnarok\skill::MAX_SKILL;
						break;
					case 'killer':
					case 'killed':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'map':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\map::MAP_NAME_LENGTH;
						break;
				}

				if ( ! $this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __get($name)
			{
				switch ($name)
				{
					case 'killer_id':
					case 'killed_id':
						return new char($this->data[$name]);
				}

				return parent::__get($name);
			}

			public function __construct($id = null, $killer = true)
			{
				$this->table     = __CLASS__;

				if ($killer)
				{
					$this->id_column = 'killer_id';
					$this->data['killed_id'] = 0;
				}
				else
				{
					$this->id_column = 'killed_id';
					$this->data['killer_id'] = 0;
				}

				parent::__construct();

				$this->data['id']        = 0;
				$this->data['time']      = null;
				$this->data['killer']    = '';
				$this->data['killed']    = '';
				$this->data['map']       = '';
				$this->data['skill']     = 0;

				if ( !is_null($id))
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}
