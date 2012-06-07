<?php

namespace gui 
{

	class charlist extends gui_base
	{
		private $job=0;
	
		public function __construct(array $data)
		{
			$this->title = 'EXP Rankings';
			if ( isset($data['job']) )
				$this->job = $data['job']; 
		}
		
		public function build()
		{
			global $dbh;
			$data = array();
			
			try 
			{
				$query = "SELECT `char`.`char_id`, `char`.`account_id`, `char`.`name`, `char`.`class`, `char`.`base_level`, `char`.`job_level`, `char`.`base_exp`, `char`.`job_exp`, `char`.`guild_id`, `login`.`sex`, `login`.`level` AS gm_level, `guild`.`name` AS gname, `d`.`value` AS `deaths`, `m`.`value` AS `mission_pts` "
						."FROM `char` "
						."LEFT JOIN `login` ON `char`.`account_id`=`login`.`account_id` " 
						."LEFT JOIN `guild` ON `char`.`guild_id`  =`guild`.`guild_id` "
						."LEFT JOIN `global_reg_value` AS `d` ON `char`.`char_id` = `d`.`char_id` AND `d`.`str`='PC_DIE_COUNTER' "
						."LEFT JOIN `global_reg_value` AS `m` ON `char`.`char_id` = `m`.`char_id` AND `m`.`str`='MD_mission_points' "
						."LEFT JOIN `global_reg_value` AS `s` ON `char`.`char_id` = `s`.`char_id` AND `s`.`str`='bgchar' "
						."WHERE `login`.`level` < 100 AND `char`.`base_level` > '0' AND `s`.`value` is null "
						."ORDER BY `char`.`base_level` DESC, `char`.`base_exp` DESC "
						."LIMIT 50";
				
				$sth = $dbh->prepare($query);
				
				$sth->execute();
				$data = $sth->fetchAll(\PDO::FETCH_ASSOC);
			}
			catch (\PDOException $e)
			{
				$this->page_string = $e->getMessage();
			}

			if ( !count($data) )
			{
				$this->page_string = 'Nothing found...';
				return $this->page_string;
			}

			$str = "\t\t<div id=\"datatable\">\n\t\t\t<table class=\"pagedata\">\n\t\t\t\t<tbody>\n";
			
			$str .="\t\t\t\t\t<tr>";
			
			$head = array('#', 'Name', 'Job', 'B.Lv', 'J.Lv', 'Guild');
			foreach ($head as $header)
			{
				$str .= '<th>'.$header.'</th>';
			}
			
			$str .="</tr>\n";
			
			$i=0;
			foreach ($data as $char)
			{
				$str .="\t\t\t\t\t".'<tr class="tr'.($i%2+1).'">'
				
				.'<td class="ar">'.++$i.'.</td>'
				.'<td class="al '.$this->namecolor($char['gm_level'], $char['sex']).'">'.$this->html_chars($char['name']).'</td>'
				.'<td class="ac">'.$this->job_name($char['class']).'</td>'
				.'<td>'.$char['base_level'].'+'.$this->format_number($char['base_exp']).'</td>'
				.'<td>'.$char['job_level'].'+'.$this->format_number($char['job_exp']).'</td>'
				.'<td>'.($char['guild_id']?('<a href="?p=guild&amp;arg=id;'.$char['guild_id'].'">'.$this->html_chars($char['gname']).'</a>'):'&nbsp;').'</td>'
				
				."</tr>\n";
			}
			
			$str .="\t\t\t\t</tbody>\t\t\t</table>\n\t\t</div>";
			
			return $str;
		}



	}


}