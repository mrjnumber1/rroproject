<?php

namespace gui
{
	class gui_base
	{
		protected $title = '';
		protected $page_heading = '';
		protected $page_string ='';
		protected $header = 'Content-Type: text/html; charset=utf-8';
		protected $show_page = true;
		
		public function __construct()
		{
			
		}
		
		public function __destruct()
		{
			
		}
		
		public function __toString() { return $this->page_string; }

		public function get_title() { return $this->title; }
		public function get_header() { return $this->header; }
		public function get_page_heading() { return $this->page_heading; }
		public function render_page() { return $this->show_page; }
		protected function html_chars($str)
		{
			return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
		}
		protected function format_number($n)
		{
			if($n<10000) return intval($n);
			if($n<100000) return sprintf('%1.1fk',$n/1000-0.05);
			if($n<1000000) return sprintf('%1.0fk',$n/1000-0.5);
			if($n<10000000) return sprintf('%1.2fM',$n/1000000-0.005);
			if($n<100000000) return sprintf('%1.1fM',$n/1000000-0.05);
			if($n<1000000000) return sprintf('%1.0fM',$n/1000000-0.5);
			if($n<10000000000) return sprintf('%1.2fB',$n/1000000000-0.005);
			return sprintf('%1.1fB',$n/1000000000-0.05);
		}
		protected function job_name($id)
		{
			$jobname=array(
				'Novice',
				'Swordman','Magician','Archer','Acolyte','Merchant','Thief',
				'Knight','Priest','Wizard','Blacksmith','Hunter','Assassin','P. Knight',
				'Crusader','Monk','Sage','Rogue','Alchemist','Bard','Dancer','P. Crusader',
				23=>'Super Novice',
				24=>'Gunslinger','Ninja',
				4001=>'High Novice',
				'High Swordman','High Magician','High Archer','High Acolyte','High Merchant','High Thief',
				'Lord Knight','High Priest','High Wizard','Mastersmith','Sniper','Assassin Cross','P. Lord Knight',
				'Paladin','Champion','Scholar','Stalker','Biochemist','Minstrel','Gypsy','P. Paladin',
				4023=>'Baby',
				'Baby Swordman','Baby Magician','Baby Archer','Baby Acolyte','Baby Merchant','Baby Thief',
				'Baby Knight','Baby Priest','Baby Wizard','Baby Blacksmith','Baby Hunter','Baby Assassin','P. Baby Knight',
				'Baby Crusader','Baby Monk','Baby Sage','Baby Rogue','Baby Alchemist','Baby Bard','Baby Dancer','P. Baby Crusader',
				'Super Baby',
				'Taekwon','Taekwon Master','Taekwon Master','Soul Linker'
			);

			if ( isset($jobname[$id]) )
				return $jobname[$id];
			else
				return 'Unknown Job ID...';
		}

		protected function namecolor($gmlvl, $sex)
		{
			$str = '';


			$str .= ($sex =='M')?'0':'1';

		//	if($gmlvl >= 99)
		//		$str .='3';
	//		else if ($gmlvl >= 3)
	//			$str .='2';
	//		else if ($gmlvl >= 2)
	//			$str .='1';
	//		else
				$str .='0';

			return 'nc'.$str;
		}


	}

}