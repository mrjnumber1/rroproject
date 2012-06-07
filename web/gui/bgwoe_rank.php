<?php
namespace gui
{
	class bgwoe_rank extends gui_base
	{
		protected $table = '';

		public function build()
		{
			global $dbh;
			$data = array();

			try
			{
				$query = "SELECT `{$this->table}`.*, `guild`.`emblem_len`, `guild`.`guild_id` AS `gid`, `guild`.`name` AS `gname`, `char`.`char_id` AS cid, `char`.`name` AS `cname`, `char`.`class`, `login`.`level` AS `gm_level`, `login`.`sex`
						FROM `{$this->table}`
						LEFT JOIN `char` ON `{$this->table}`.`char_id` = `char`.`char_id`
						LEFT JOIN `login` ON `char`.`account_id` = `login`.`account_id`
						LEFT JOIN `guild` ON `char`.`guild_id` = `guild`.`guild_id`
						WHERE `login`.`level` < 100 ";

				if ($this->table == 'bg_stats')
					$query .="ORDER BY `points` DESC ";
				$query .=" LIMIT 50";

				$sth = $dbh->prepare($query);

				if (!$sth->execute())
					throw new \PDOException("{$this->table} data did not load aaa.");

				$data = $sth->fetchAll(\PDO::FETCH_ASSOC);
			}
			catch (\PDOException $e)
			{
				$this->page_string = $e->getMessage();
				return $this->page_string;
			}

			if ( !count($data) )
			{
				$this->page_string = 'Nothing found...';
				return $this->page_string;
			}
			$str = "\t\t<div id=\"datatable\">".PHP_EOL."\t\t\t<table class=\"pagedata\">".PHP_EOL."\t\t\t\t<tbody>";

			$nl = PHP_EOL."\t\t\t\t\t";
			$i = 0;
			foreach ($data as $char)
			{

				$ebm_url = '';
				if (intval($char['emblem_len']) <= 0)
					$ebm_url='images/no_ebm.png';
				else
					$ebm_url = '?p=emblem&amp;arg=id;'.$char['gid'];

				$str .= $nl;
				$nl  .= "\t";
				$str .="<tr>".$nl;
				$str .="<th> #".++$i."</th>".$nl;
				$str .="<th colspan=\"2\"><a href=\"?p=charinfo&amp;arg=id;".$char['cid']."\"> ".$this->html_chars($char['cname'])."</a></th>".$nl;
				$gname = $char['gid']?$this->html_chars($char['gname']):'&nbsp;';
				if ($char['gid'])
				{
					if(stripos($ebm_url, 'no_ebm'))
					{
						$str .="<th><img src=\"".$ebm_url."\" alt=\"No Emblem\"/></th>".$nl;
						$str .="<th class=\"al\" colspan=\"2\"> <a href=\"?p=guild&amp;arg=id;{$char['gid']}\">{$gname}</a></th>".$nl;
					}
					else
					{
						$str .="<th class=\"ar\"><img class=\"ebmborder\" src=\"".$ebm_url."\" alt=\"{$gname}\"/> </th>".$nl;
						$str .="<th class=\"al\" colspan=\"2\"> <a href=\"?p=guild&amp;arg=id;{$char['gid']}\">{$gname}</a></th>".$nl;

					}
				}
				else
				{
					$str .="<th colspan=\"3\"><img src=\"".$ebm_url."\" alt=\"No Guild\"/>&nbsp;</th>".$nl;
				}

				$str .="<th colspan=\"2\">{$this->job_name($char['class'])}</th>".$nl;
				if ($this->table == 'bg_stats')
				{
					$str .="</tr>".$nl."<tr class =\"trh\">";
					$str .="<td>Points</td>".$nl;
					$str .="<td>Rank Points</td>".$nl;
					$str .="<td>Rank Games</td>".$nl;
					$str .="<td>Wins</td>".$nl;
					$str .="<td>Losses</td>".$nl;
					$str .="<td>Leader Wins</td>".$nl;
					$str .="<td>Leader Losses</td>".$nl;
					$str .="<td>Quits</td>".$nl;

					$str .="</tr>".$nl."\t<tr class=\"tr2\">";
					$str .="<td>".intval($char['points']/10)."</td>".$nl;
					$str .="<td>".intval($char['rank_points']/10)."</td>".$nl;
					$str .="<td>".intval($char['rank_games'])."</td>".$nl;
					$str .="<td>".intval($char['cq_wins'])."</td>".$nl;
					$str .="<td>".intval($char['cq_lost'])."</td>".$nl;
					$str .="<td>".intval($char['leader_win'])."</td>".$nl;
					$str .="<td>".intval($char['leader_lost'])."</td>".$nl;
					$str .="<td>".intval($char['deserter'])."</td>".$nl;
				}


				$str .="</tr>".$nl."\t<tr class=\"trh\">";
				$str .="<td>Kills</td>".$nl;
				$str .="<td>Deaths</td>".$nl;
				$str .="<td>K:D Ratio</td>".$nl;
				$str .="<td>Top Dmg</td>".$nl;
				$str .="<td colspan=\"2\">Dmg Done</td>".$nl;
				$str .="<td colspan=\"2\">Dmg Recv</td>".$nl;

				$str .="</tr>".$nl."\t<tr class=\"tr2\">";
				$str .="<td>".intval($char['kill_count'])."</td>".$nl;
				$str .="<td>".intval($char['death_count'])."</td>".$nl;
				$ratio = $char['death_count']?(sprintf('%1.3f', ($char['kill_count']/$char['death_count']))):'-';
				$str .="<td>".$ratio."</td>".$nl;
				$str .="<td>".intval($char['top_damage'])."</td>".$nl;
				$str .="<td colspan=\"2\">{$this->format_number($char['damage_done'])}</td>".$nl;
				$str .="<td colspan=\"2\">{$this->format_number($char['damage_received'])}</td>".$nl;

				$str .="</tr>".$nl."\t<tr class=\"trh\">";
				$str .="<td>Emp Kills</td>".$nl;
				$str .="<td>Emp Dmg</td>".$nl;
				$str .="<td>Barricade Kills</td>".$nl;
				$str .="<td colspan=\"2\">Barricade Dmg</td>".$nl;
				$str .="<td>GStone Kills</td>".$nl;
				$str .="<td colspan=\"2\">GStone Dmg</td>".$nl;

				$str .="</tr>".$nl."\t<tr class=\"tr2\">";
				$str .="<td>".intval($char['emperium_kill'])."</td>".$nl;
				$str .="<td>".$this->format_number($char['emperium_damage'])."</td>".$nl;
				$str .="<td>".intval($char['barricade_kill'])."</td>".$nl;
				$str .="<td colspan=\"2\">".$this->format_number($char['barricade_damage'])."</td>".$nl;
				$str .="<td>".intval($char['gstone_kill'])."</td>".$nl;
				$str .="<td colspan=\"2\">".$this->format_number($char['gstone_damage'])."</td>".$nl;

				if (($char['support_skills_used']||$char['wrong_support_skills_used']||$char['healing_done']||$char['wrong_healing_done']))
				{
					$str .="</tr>".$nl."\t<tr class=\"trh\">";
					$str .="<td colspan=\"2\">Correct Support</td>".$nl;
					$str .="<td colspan=\"2\">Wrong Support</td>".$nl;
					$str .="<td colspan=\"2\">Correct Healing</td>".$nl;
					$str .="<td colspan=\"2\">Wrong Healing</td>".$nl;
					$str .="</tr>".$nl."\t<tr class=\"tr2\">";
					$str .="<td colspan=\"2\">{$char['support_skills_used']}</td>".$nl;
					$str .="<td colspan=\"2\">{$char['wrong_support_skills_used']}</td>".$nl;
					$str .="<td colspan=\"2\">{$char['healing_done']}</td>".$nl;
					$str .="<td colspan=\"2\">{$char['wrong_healing_done']}</td>".$nl;
				}


				$j = 0;
				$amounts = array();
				$names = array();
				$items = array('Yellow Gems', 'Red Gems', 'Blue Gems', 'EDPs', 'Zeny', 'Ammo', 'Spirit Balls');
				$keys = array('yellow_gemstones', 'red_gemstones', 'blue_gemstones', 'poison_bottles', 'zeny_used', 'ammo_used', 'spiritb_used');
				$idx = 0;
				foreach ($keys as $key)
				{
					if (array_key_exists($key, $char) && $char[$key] > 0)
					{
						$amounts[$j] = intval($char[$key]);
						$names[$j] = $items[$idx];
						++$j;
					}
					++$idx;
				}

				$str .="</tr>".$nl."\t<tr class=\"trh\">";
				$str .="<td colspan=\"2\">HP Potions</td>".$nl;
				$str .="<td colspan=\"2\">SP Potions</td>".$nl;
				$str .="<td>SP Used</td>".$nl;

				if ($j == 3)
					$str .="<td>{$names[0]}</td>".$nl;
				else if ($j == 2)
					$str .="<td colspan=\"2\">{$names[0]}</td>".$nl;
				else if ($j == 1)
					$str .="<td colspan=\"3\">{$names[0]}</td>".$nl;
				else
					$str .="<td colspan=\"4\">&nbsp;</td>".$nl;

				$cols = $j-1;
				if($cols > 0)
					$str .="<td>{$names[1]}</td>".$nl;
				$cols -= 1;

				if($cols > 0)
					$str .="<td>{$names[2]}</td>".$nl;

				$hppots = array_key_exists('hp_heal_potions', $char)?$char['hp_heal_potions']:0;
				$sppots = array_key_exists('sp_heal_potions', $char)?$char['sp_heal_potions']:0;
				$spused = array_key_exists('sp_used', $char)?$char['sp_used']:0;

				$str .="</tr>".$nl."\t<tr class=\"tr2\">";
				$str .="<td colspan=\"2\">".$this->format_number($hppots)."</td>".$nl;
				$str .="<td colspan=\"2\">".$this->format_number($sppots)."</td>".$nl;
				$str .="<td>".$this->format_number($spused)."</td>".$nl;

				if ($j == 3)
					$str .="<td>{$amounts[0]}</td>".$nl;
				else if ($j == 2)
					$str .="<td colspan=\"2\">{$amounts[0]}</td>".$nl;
				else if ($j == 1)
					$str .="<td colspan=\"3\">{$amounts[0]}</td>".$nl;
				else
					$str .="<td colspan=\"4\">&nbsp;</td>".$nl;

				$cols = $j-1;
				if($cols > 0)
					$str .="<td>{$amounts[1]}</td>".$nl;
				$cols -= 1;

				if($cols > 0)
					$str .="<td>{$amounts[2]}</td>".$nl;

			}


			$str .= PHP_EOL."\t\t\t\t</tbody>".PHP_EOL."\t\t\t</table>".PHP_EOL."\t\t</div>";



			$this->page_string = $str;
			return $str;
		}

		public function __construct()
		{
			if ( get_class($this) == 'bgwoe_rank' )
			{
				print 'cannot instantiate base class! get out!';
			}
		}

	}
}
