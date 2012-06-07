<?php
namespace gui
{
	class guild extends gui_base
	{
		private $id = 0;
		private $data = array();

		public function build()
		{
			global $dbh;

			//$this->title = $this->data['name'];
			if (!$this->id)
			{
				$this->page_string = 'Guild not found';
				return $this->page_string;
			}

			$data = array();
			try
			{
				$query = "SELECT `guild`.`name`, `guild`.`char_id`, `guild`.`emblem_len` FROM `guild` WHERE `guild_id` = :guild_id LIMIT 1";
				$sth = $dbh->prepare($query);
				$sth->bindValue(':guild_id', $this->id);
				$sth->execute();
				$data = $sth->fetch(\PDO::FETCH_ASSOC);
				$this->title = $data['name'];
				$leader_id = $data['char_id'];

				$sth = null;

				$ebm_url = '';
				if (intval($data['emblem_len']) <= 0)
					$ebm_url='images/no_ebm.png';
				else
					$ebm_url = '?p=emblem&arg=id;'.$this->id;

				$data = null;

				$this->page_heading = '<img src="'.$ebm_url.'"/> '.$this->title;

				$query = "SELECT `char`.`char_id`, `char`.`name`, `char`.`class`, `char`.`base_level`, `char`.`job_level`, `login`.`sex`, `login`.`level` AS gm_level, `guild_member`.`exp` AS `tax_exp`, `guild_position`.`name` AS `title`, `guild_position`.`exp_mode` AS `tax_rate` "
						."FROM `char` "
						."LEFT JOIN `login` ON `char`.`account_id`=`login`.`account_id` "
						."LEFT JOIN `guild` ON `char`.`guild_id` = `guild`.`guild_id` "
						."LEFT JOIN `guild_member` ON `char`.`char_id` = `guild_member`.`char_id` "
						."LEFT JOIN `guild_position` ON `char`.`guild_id` = `guild_position`.`guild_id` AND `guild_member`.`position` = `guild_position`.`position`"
						."WHERE `login`.`level` < 100 AND `char`.`guild_id` = :guild_id "
						."ORDER BY `char`.`base_level` DESC, `char`.`base_exp` DESC "
						."LIMIT 76";
				$sth = $dbh->prepare($query);
				$sth->bindValue(':guild_id', $this->id);
				$sth->execute();
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

		//	$str = "\t\t<div id=\"datatable\">\n\t\t\t<table class=\"pagedata\">\n\t\t\t\t<tbody>\n";
		//	$str .="\t\t\t\t</tbody>\t\t\t</table>\n\t\t</div>";

			$str = "\t\t<div id=\"datatable\">\n\t\t\t<table class=\"pagedata\">\n\t\t\t\t<tbody>\n";

			$str .="\t\t\t\t\t<tr>";

			$head = array('#', 'Name', 'Job', 'B.Lv', 'J.Lv', 'Title', 'Tax EXP', 'Tax%');
			foreach ($head as $header)
			{
				$str .= '<th>'.$header.'</th>';
			}

			$str .="</tr>\n";

			$i=0;
			foreach ($data as $char)
			{
				$str .="\t\t\t\t\t".'<tr class="tr'.($i%2+1).($char['char_id']==$leader_id?'h':'').'">'

					.'<td class="ar">'.++$i.'.</td>'
					.'<td class="al '.$this->namecolor($char['gm_level'], $char['sex']).'">'.$this->html_chars($char['name']).'</td>'
					.'<td class="ac">'.$this->job_name($char['class']).'</td>'
					.'<td>'.$char['base_level'].'</td>'
					.'<td>'.$char['job_level'].'</td>'
					.'<td>'.(($char['title'])?($this->html_chars($char['title'])):('&nbsp;')).'</td>'
					.'<td>'.$this->format_number($char['tax_exp']).'</td>'
					.'<td>'.intval($char['tax_rate']).'</td>'

					."</tr>\n";
			}

			$str .="\t\t\t\t</tbody>\t\t\t</table>\n\t\t</div>";

			return $str;
		}

		public function build_guild_info()
		{
			global $dbh;

			try
			{
				$query = "SELECT `guild_id`, `name`, `guild_lv`, `char_id`, `master`, `max_member`, `average_lv`, `exp`, `next_exp`, `emblem_len`, `emblem_id`, `emblem_data` "
						."FROM `guild` "
						."WHERE `guild_id` = :guild_id "
						."LIMIT 1";

				$sth = $dbh->prepare($query);

				$sth->bindValue(':guild_id', $this->id, \PDO::PARAM_INT);

				$result = $sth->execute();

				if (!$result)
				{
					$this->page_string = 'Guild not found.';
					return false;
				}

				$this->data = $sth->fetch(\PDO::FETCH_ASSOC);
			}
			catch (\PDOException $e)
			{
				$this->page_string = $e->getMessage();
				return false;
			}
		}/**/

		public function __construct(array $data)
		{
			if ( isset($data['id']) && $data['id'] > 0)
			{
				$this->id = $data['id'];

				return ($this->build_guild_info() != false);
			}
			else
			{
				$this->page_string = 'No Guild info found..';
				return false;
			}
		}
	}
}