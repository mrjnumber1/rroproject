<?php

namespace dao
{
	class bg_stats extends db
	{
		public function __set($name, $value)
		{
			switch($name)
			{
				case 'damage_done':
				case 'damage_received':
				case 'emperium_damage':
				case 'barricade_damage':
				case 'gstone_damage':
					$this->check_value($name, $value, $this->min, $this->uint_max);
					break;
				case 'char_id':
				case 'top_damage':
				case 'emperium_kill':
				case 'barricade_kill':
				case 'gstone_kill':
				case 'cq_wins':
				case 'cq_lost':
				case 'kill_count':
				case 'death_count':
				case 'win':
				case 'lost':
				case 'tie':
				case 'leader_win':
				case 'leader_lost':
				case 'leader_tie':
				case 'deserter':
				case 'sp_heal_potions':
				case 'hp_heal_potions':
				case 'yellow_gemstones':
				case 'red_gemstones':
				case 'blue_gemstones':
				case 'poison_bottles':
				case 'acid_demonstration':
				case 'acid_demonstration_fail':
				case 'support_skills_used':
				case 'healing_done':
				case 'wrong_support_skills_used':
				case 'wrong_healing_done':
				case 'sp_used':
				case 'zeny_used':
				case 'spiritb_used':
				case 'ammo_used':
				case 'points':
				case 'rank_points':
				case 'rank_games':
					$this->check_value($name, $value, $this->int_min, $this->int_max);
					break;
			}

			parent::__set($name, $value);
		}

		public function __construct($id=null)
		{
			$this->table     = 'bg_stats';
			$this->id_column = 'char_id';

			$this->data['char_id']          = null;
			$this->data['top_damage']       = 0;
			$this->data['damage_done']      = 0;
			$this->data['damage_received']  = 0;
			$this->data['emperium_damage']  = 0;
			$this->data['barricade_damage'] = 0;
			$this->data['gstone_damage']    = 0;
			$this->data['emperium_kill']    = 0;
			$this->data['barricade_kill']   = 0;
			$this->data['gstone_kill'] = 0;
			$this->data['cq_wins']     = 0;
			$this->data['cq_lost']     = 0;
			$this->data['kill_count']  = 0;
			$this->data['death_count'] = 0;
			$this->data['win']  = 0;
			$this->data['lost'] = 0;
			$this->data['tie']  = 0;
			$this->data['leader_win']  = 0;
			$this->data['leader_lost'] = 0;
			$this->data['leader_tie']  = 0;
			$this->data['deserter']    = 0;
			$this->data['sp_heal_potions']    = 0;
			$this->data['hp_heal_potions']    = 0;
			$this->data['yellow_gemstones']   = 0;
			$this->data['red_gemstones']      = 0;
			$this->data['blue_gemstones']     = 0;
			$this->data['poison_bottles']     = 0;
			$this->data['acid_demonstration'] = 0;
			$this->data['acid_demonstration_fail']   = 0;
			$this->data['support_skills_used']       = 0;
			$this->data['healing_done']              = 0;
			$this->data['wrong_support_skills_used'] = 0;
			$this->data['wrong_healing_done']        = 0;
			$this->data['sp_used']      = 0;
			$this->data['zeny_used']    = 0;
			$this->data['spiritb_used'] = 0;
			$this->data['ammo_used']    = 0;
			$this->data['points']       = 0;
			$this->data['rank_points']  = 0;
			$this->data['rank_games']   = 0;

			if ( !is_null($id))
			{
				$this->load_by_id(intval($id));
			}
		}
	}
}
