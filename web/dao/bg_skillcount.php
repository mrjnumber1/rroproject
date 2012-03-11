<?php
	namespace dao
	{
		class bg_skillcount extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\ragnarok\number::INT_MAX;

				switch ($name)
				{
					case 'char_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						$max = \config\constants\ragnarok\number::UINT_MAX;
						break;
					case 'id':
						$max = \config\constants\ragnarok\skill::MAX_SKILL;
						break;
					case 'count':
						$max = \config\constants\ragnarok\number::UINT_MAX;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}
			}

			public function __construct($id = null)
			{

				$this->table = 'bg_skillcount';
				$this->id_column = 'char_id';

				parent::__construct();

				$this->data['id']   = 0;
				$this->data['count']= 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}