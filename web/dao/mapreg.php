<?php
	namespace dao
	{
		class mapreg extends db
		{

			public function __set($key, $value)
			{
				if ($key == $this->id_column)
				{
					$name = $key;

					$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
					$max = 32;

					$this->set_value($name, $value, $min, $max);
					return;
				}

				if ( !array_key_exists($key, $this->data) )
				{
					$this->data[$key] = array();
				}

				$this->data[$key] = array_merge($this->data[$key], (array)$value);
			}

			public function __construct($var = null)
			{
				$this->table = 'mapreg';
				$this->id_column = 'varname';

				parent::__construct();

				$this->data[$this->id_column] = '';
				// TODO: will have to turn this into keys and values from load_by_query :)
				$this->values = array();

				if ( !is_null($var) )
				{
					$this->load_by_id( \lib\string\left($var, 32) );
				}
			}
		}
	}