<?php

namespace dao
{
	class bg_stats extends db
	{
		public function __set($name, $value)
		{
			$min = 0;
			$max = \config\constants\number::INT_MAX;

			switch ($name)
			{
				case 'damage_done':
				case 'damage_received':
				case 'emperium_damage':
				case 'barricade_damage':
				case 'gstone_damage':
					$max = \config\constants\number::UINT_MAX;
					break;
				case 'char_id':
					$min = \config\constants\ragnarok\character::START_CHAR_NUM;
					break;
			}

			if( ! $this->set_value($name, $value, $min, $max) )
			{
				parent::__set($name, $value);
			}
		}

		public function __construct($id=null)
		{
			$this->table     = 'bg_stats';
			$this->id_column = 'char_id';

			parent::__construct();

			$this->data['top_damage']               = 0;
			$this->data['damage_done']              = 0;
			$this->data['damage_received']          = 0;
			$this->data['emperium_damage']          = 0;
			$this->data['barricade_damage']         = 0;
			$this->data['gstone_damage']            = 0;
			$this->data['emperium_kill']            = 0;
			$this->data['barricade_kill']           = 0;
			$this->data['gstone_kill']              = 0;

			$this->data['points']                   = 0;
			$this->data['rank_points']              = 0;
			$this->data['rank_games']               = 0;

			$this->data['cq_wins']                  = 0;
			$this->data['cq_lost']                  = 0;
			$this->data['win']                      = 0;
			$this->data['lost']                     = 0;
			$this->data['tie']                      = 0;
			$this->data['leader_win']               = 0;
			$this->data['leader_lost']              = 0;
			$this->data['leader_tie']               = 0;
			$this->data['deserter']                 = 0;

			$this->data['kill_count']               = 0;
			$this->data['death_count']              = 0;

			$this->data['sp_heal_potions']          = 0;
			$this->data['hp_heal_potions']          = 0;
			$this->data['yellow_gemstones']         = 0;
			$this->data['red_gemstones']            = 0;
			$this->data['blue_gemstones']           = 0;
			$this->data['poison_bottles']           = 0;
			$this->data['acid_demonstration']       = 0;
			$this->data['acid_demonstration_fail']  = 0;
			$this->data['support_skills_used']      = 0;
			$this->data['healing_done']             = 0;
			$this->data['wrong_support_skills_used']= 0;
			$this->data['wrong_healing_done']       = 0;
			$this->data['sp_used']                  = 0;
			$this->data['zeny_used']                = 0;
			$this->data['spiritb_used']             = 0;
			$this->data['ammo_used']                = 0;

			if ( !is_null($id) )
			{
				$this->load_by_id( intval($id) );
			}
		}
	}
}
