<?php
	namespace dao
	{
		class quest extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UMINT_MAX;

				switch ($name)
				{
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'quest_id':
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'state':
						$min = \config\constants\ragnarok\quest::Q_INACTIVE;
						$max = \config\constants\ragnarok\quest::Q_COMPLETE;
						break;
					case 'time':
						$max = \config\constants\number::UINT_MAX;
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
					case 'time':
						return new \DateTime($this->data[$name]);
				}

				return parent::__get($name);
			}
			public function __construct($id = null, $char_id = true)
			{
				$this->table = 'quest';

				if ($char_id)
				{
					$this->id_column = 'char_id';
					$this->data['quest_id'] = 0;
				}
				else
				{
					$this->id_column = 'quest_id';
					$this->data['char_id'] = 0;
				}

				parent::__construct();

				$this->data['count1'] = 0;
				$this->data['count2'] = 0;
				$this->data['count3'] = 0;
				$this->data['time'] = 0;
				$this->data['state'] = \config\constants\ragnarok\quest::Q_INACTIVE;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}