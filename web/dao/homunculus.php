<?php

	namespace dao
	{
		class homunculus extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'class':
						$min = \config\constants\ragnarok\homunculus::HM_CLASS_BASE;
						$max = \config\constants\ragnarok\homunculus::HM_CLASS_MAX;
						break;
					case 'name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'level':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_LEVEL;
						break;
					case 'intimacy':
						$min = 1;
						$max = \config\constants\ragnarok\homunculus::MAX_INTIMACY;
						break;
						break;
					case 'str':
					case 'agi':
					case 'vit':
					case 'dex':
					case 'int':
					case 'luk':
						$min = 1;
						$max = \config\constants\ragnarok\character::MAX_STAT * 10;
						break;
					case 'hunger':
						$min = 0;
						$max = \config\constants\ragnarok\homunculus::MAX_HUNGER;
						break;
					case 'rename_flag':
					case 'alive':
					case 'vaporize':
						$max = 1;
						break;
					case 'skill_point':
						// TODO: arbitrarily guessed; will need true function based off level & hom type & skills :)
						$max = 30;
						break;
					case 'hp':
						$max = $this->data['max_hp'];
						break;
					case 'sp':
						$max = $this->data['max_sp'];
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

				}

				parent::__get($name);
			}

			public function __construct($id = null, $homun = true)
			{
				$this->table = 'homunculus';

				if ($homun)
				{
					$this->id_column = 'homun_id';
					$this->data['char_id'] = 0;
				}
				else
				{
					$this->id_column = 'char_id';
					$this->data['homun_id'] = 0;
				}

				parent::__construct();

				$this->data['class'] = \config\constants\ragnarok\homunculus::HM_CLASS_BASE;
				$this->data['name'] = '';
				$this->data['level'] = 0;
				$this->data['exp'] = 0;
				$this->data['intimacy'] = 0;
				$this->data['hunger'] = 0;
				$this->data['str'] = 1;
				$this->data['agi'] = 1;
				$this->data['vit'] = 1;
				$this->data['int'] = 1;
				$this->data['dex'] = 1;
				$this->data['luk'] = 1;
				$this->data['hp'] = 1;
				$this->data['max_hp'] = 1;
				$this->data['sp'] = 1;
				$this->data['max_sp'] = 1;
				$this->data['skill_point'] = 0;
				$this->data['alive'] = 1;
				$this->data['rename_flag'] = 0;
				$this->data['vaporize'] = 0;


				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	}