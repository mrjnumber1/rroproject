<?php
namespace gui
{
	class guildlist extends gui_base
	{
		public function build()
		{
			global $dbh;

			$data = array();
			try
			{
				$query = "SELECT `guild`.*, `char`.`name` AS `cname`, count(*) AS `members`, `login`.`sex`, `login`.`level` AS `gm_level` "
						."FROM `guild` "
						."LEFT JOIN `char`  ON `guild`.`char_id` = `char`.`char_id` "
						."LEFT JOIN `char` AS `c2` ON `guild`.`guild_id`=`c2`.`guild_id` "
						."LEFT JOIN `login` ON `login`.`account_id` = `char`.`account_id` "
						."WHERE `login`.`level` < 100 "
						."GROUP BY `guild`.`guild_id` "
						."LIMIT 30";

				$sth = $dbh->prepare($query);
				$res = $sth->execute();

				if (!$res)
					return false;
				else
					$data = $sth->fetchAll(\PDO::FETCH_ASSOC);
			}
			catch (\PDOException $e)
			{
				$this->page_string = $e->getMessage();
				return false;
			}
			if ( !count($data) )
			{
				$this->page_string = 'Nothing found...';
				return $this->page_string;
			}

			$str = "\t\t<div id=\"datatable\">\n\t\t\t<table class=\"pagedata\">\n\t\t\t\t<tbody>\n";

			$str .="\t\t\t\t\t<tr>";


			$head = array('#', '&nbsp;', 'Name', 'Leader', 'Level', '+EXP', 'Members', 'Avg. Lv');
			foreach ($head as $header)
			{
				$str .= '<th>'.$header.'</th>';
			}

			$str .="</tr>\n";

			$i=0;
			foreach ($data as $guild)
			{
				$ebm_url = '';

				if (intval($guild['emblem_len']) <= 0)
					$ebm_url='images'.DIRECTORY_SEPARATOR.'no_ebm.png';
				else
					$ebm_url = '?p=emblem&amp;arg=id;'.$guild['guild_id'];

				$str .="\t\t\t\t\t".'<tr class="tr'.($i%2+1).'">'

					.'<td class="ar">'.++$i.'.</td>'
					.'<td width="24"> <img src="'.$ebm_url.'" />'.'</td>'
					.'<td class="al"><a href="?p=guild&amp;arg=id;'.$guild['guild_id'].'">'.$this->html_chars($guild['name']).'</a></td>'
					.'<td class="'.$this->namecolor($guild['gm_level'], $guild['sex']).'">'.$this->html_chars($guild['cname']).'</td>'
					.'<td class="ac">'.intval($guild['guild_lv']).'</td>'
					.'<td>'.$this->format_number($guild['exp']).'</td>'
					.'<td class="ac">'.intval($guild['members']).'/'.intval($guild['max_member']).'</td>'
					.'<td class="ac">'.intval($guild['average_lv']).'</td>'

					."</tr>\n";
			}

			$str .="\t\t\t\t</tbody>\t\t\t</table>\n\t\t</div>";

			return $str;
		}

		public function __construct(array $data)
		{
			$this->title = 'Guilds';
			return true;
		}
	}
}