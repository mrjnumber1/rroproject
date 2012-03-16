<?php
	namespace dao
	{
		class sstatus extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX;

				switch ($name)
				{
					case 'index':
						$max = \config\constants\number::UTINT_MAX;
						break;
					case 'name':
						$max = 255;
						break;
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__set($name, $value);
				}

			}
			public function __construct($id = null)
			{
				$this->table = 'sstatus';
				$this->id_column = 'index';

				parent::__construct();

				$this->data['name'] = '';
				$this->data['user'] = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}

		}
	}