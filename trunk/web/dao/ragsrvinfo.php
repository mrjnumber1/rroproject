<?php
	namespace dao
	{
		class ragsrvinfo extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::UINT_MAX;

				switch ($name)
				{
					case 'index':
						$max = \config\constants\number::INT_MAX;
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
				$this->table = 'ragsrvinfo';
				$this->id_column = 'index';

				parent::__construct();

				$this->data['exp' ] = 0;
				$this->data['jexp'] = 0;
				$this->data['drop'] = 0;
				$this->data['name'] = '';

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}

			}
		}
	}