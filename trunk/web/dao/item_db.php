<?php
	namespace dao
	{	
		class item_db extends db
		{

			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'attack':
						// arbitrarily set to prevent exploits
						$max = 450;
						//$max = \config\constants\number::USINT_MAX;
						break;
					case 'defense':
						// also arbitrarily set to prevent exploits
						$max = \config\constants\ragnarok\character::MAX_DEFENSE / 3;
						break;
					case 'equip_genders':
						//$max = 1 + \config\constants\ragnarok\enums\sex::Female | \config\constants\ragnarok\enums\sex::Male;
						break;
					case 'equip_jobs':
						$max = \config\constants\number::UINT_MAX;
						break;
					case 'equip_upper':
						//$max = \config\constants\ragnarok\enums\equip_upper::Normal
						//		| \config\constants\ragnarok\enums\equip_upper::Upper
						//		| \config\constants\ragnarok\enums\equip_upper::Baby;
						break;
					case 'equip_locations':
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'name_english':
					case 'name_japanese':
						$max = 50;
						break;
					case 'price_buy':
					case 'price_sell':
						//$max = \config\constants\ragnarok\character::MAX_ZENY / 10000;
						$max = 600000;
						// arbitrarily set to prevent exploits, hopefully
						break;
					case 'range':
						// arbitrarily found with roguemaster bow
						// any higher would be insane
						$max = 11;
						break;
					case 'refineable':
						$value = (bool)$value;
						break;
					case 'type':
						//$max = \config\constants\ragnarok\enums\item_type::max_value();
						break;
					case 'view':
						$max = \config\constants\number::USINT_MAX;
						break;
					case 'weapon_level':
						$max = 4;
						break;
					case 'weight':
						// arbitrary number again!
						$max = 10000;
						break;
					case 'script':
					case 'equip_script':
					case 'unequip_script':
						$max = 1024;
						// 65535 is just -excessive-.
				}

				if ( !$this->set_value($name, $value, $min, $max) )
				{
					parent::__construct($name, $value);
				}
			}

			public function __construct($id = null, $use2 = false)
			{
				$this->table = 'item_db';
				if ($use2)
				{
					$this->table .= '2';
				}
				$this->id_column = 'id';

				parent::__construct();

				$this->data = array(
					'attack' => 0,
					'defence'=> 0,
					'equip_genders' => 2, // male | female
					'equip_jobs' => 0xFFFFFFFF, // all classes equipable
					'equip_script' => '',
					'equip_upper' => 7, // normal, reborn, baby
					'name_english' => '',
					'name_japanese' => '',
					'price_buy' => 20,
					'price_sell' => 10,
					'range' => 0,
					'refineable' => 0,
					'script' => '',
					'slots' => 0,
					'type' => 0,
					'unequip_script' => '',
					'view' => 0,
					'weapon_level' => 0,
					'weight' => 0,

					);

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}
		}
	
	}