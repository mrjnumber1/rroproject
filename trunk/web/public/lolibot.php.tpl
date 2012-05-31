<html>
<head><title>ohgod why</title></head>
<body>

<?php
	$dbh = new PDO('mysql:host=HOSTNAMEEEEEEEEE;dbname=DBDBDBDBDBDBDBDBD;charset=UTF-8', "USERUSERUSER", "PWPWPWPWPWPPWPW");
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	function item_search($val)
	{
		global $dbh;
		$query = "SELECT `id`, `name_english`, `name_japanese`, `slots`, `price_buy`, `weight`, `script`, `equip_script`, `unequip_script` FROM `item_db` WHERE ";
		
		if ( is_numeric($val) )
		{
			$where = "`id`=:id ";
			$query = $query.$where." LIMIT 5";
			
			$sth = $dbh->prepare($query);
			
			$sth->bindParam(":id", $val, PDO::PARAM_INT);
		}
		else if (strpos($val, "_") !== FALSE)
		{
			$where = "`name_english` LIKE :name ";
			$query = $query.$where." LIMIT 5";
			
			$sth = $dbh->prepare($query);
			
			$val = "%".$val."%";
			
			$sth->bindValue(":name", $val, PDO::PARAM_STR);
			var_dump($val);
		}
		else
		{
			$where = "`name_japanese` LIKE :name ";
			$query = $query.$where." LIMIT 5";

			$sth = $dbh->prepare($query);

			$val = "%".$val."%";
			$sth->bindValue(":name", $val, PDO::PARAM_STR);
			var_dump($val);
		}
		
		$sth->execute();

		//print $query.' val: '.$val;
		
		$sth->debugDumpParams();
		
		$i = 0;
		while ( ($item = $sth->fetch(PDO::FETCH_ASSOC)) )
		{
			//BOLD item: UNBOLD 'name_english'/'name_japanese' [slots] (id)  Buy: price_buy Weight: weight.1f
			print 
				(is_numeric($val)
				? "item ".++$i.": "
				: "")
				
				."'".$item['name_english']."' / '".$item['name_japanese']."' ".($item['slots']?"[".$item['slots']."] ":"")
				 ."(".$item['id'].") ".( ($item['price_buy']!=20) ? ("| Price: (".$item['price_buy']."/".intval($item['price_buy']/2).")") :("") )." | Weight: ".floatval($item['weight']/10). PHP_EOL.
				 ($item['script']?(" | script: {".$item['script']."}".PHP_EOL."" ):"").
				 ($item['equip_script']?(" | equip-script: {".$item['equip_script']."}".PHP_EOL."" ):"").
				 ($item['unequip_script']?(" | unequip-script: {".$item['unequip_script']."}".PHP_EOL."" ):"");
		}
		
		
	}
	
	function get_item_info($val)
	{
		global $dbh;
		
		if (is_numeric($val) )
		{
			$query = "SELECT `id`,`name_japanese`, `name_english`,`slots` FROM `item_db` WHERE `id`=:id LIMIT 3";
			
			$sth = $dbh->prepare($query);
			
			$sth->bindValue(":id", $val, PDO::PARAM_INT);
		}
		else 
		{
			$query = "SELECT `id`,`name_japanese`, `name_english`,`slots` FROM `item_db` WHERE `name_japanese` LIKE ? OR `name_english` LIKE ? LIMIT 3";
			
			$sth = $dbh->prepare($query);
			$val = "%{$val}%";
			$sth->bindValue(1, $val, PDO::PARAM_STR);
			$sth->bindValue(2, $val, PDO::PARAM_STR);
		}
		
		$sth->execute();
		
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function mob_search($val)
	{
		global $dbh;
		$sizes = array("Small", "Medium", "Large");
		$races = array("Formless", "Undead", "Beast", "Plant", "Insect", "Fish", "Demon", "DemiHuman", "Angel", "Dragon", "Boss", "NonBoss");
		$elements = array("Neutral", "Water", "Earth", "Fire", "Wind", "Poison", "Holy", "Dark", "Ghost", "Undead");
		$modes = array("Can Move", "Looter", "Aggressive", "Assist", "Cast Sensor (Idle)", "Boss-Type", "Plant-Type", "Can-Attack", "Hidden Detector", "Cast Sensor (Chase)", "Change Chase", "Aggressive", "Change Target (Melee)", "Change Target (Chase)", "Ignores Targets above level %d");
		
		if ( is_numeric($val) )
		{
			$query = "SELECT * FROM `mob_db` WHERE `ID`=:ID LIMIT 3";
			
			$sth = $dbh->prepare($query);
			
			$sth->bindValue(":ID", $val, PDO::PARAM_STR);
		}
		else
		{
			$query = "SELECT * FROM `mob_db` WHERE `Sprite` LIKE ? OR `iName` LIKE ? OR `kName` LIKE ?";
			
			$sth = $dbh->prepare($query);
			
			$sth->bindValue(1, $val, PDO::PARAM_STR);
			$sth->bindValue(2, $val, PDO::PARAM_STR);
			$sth->bindValue(3, $val, PDO::PARAM_STR);
		}
		
		$sth->execute();
		
		$rows = 0;
		
		while ( ($mob = $sth->fetch(PDO::FETCH_ASSOC)) )
		{
			$type = "Mob";
			$expmult = 15;
			if ($mob['MEXP'])
			{
				$expmult *= 10;
				$type = "MVP";
			}
			else if ($mob['Mode']&32)
				$type = "Boss";
			else
				$type = "Mob";
				
			printf(" %s: '%s'/'%s'/'%s' (%d)", $type, $mob['Sprite'], $mob['iName'], $mob['kName'], $mob['ID']);
			print PHP_EOL;
			
			printf("   Lv: %d | HP: %s | Base EXP: %s | Job EXP: %s", $mob['LV'], number_format($mob['HP']), number_format($mob['EXP']*$expmult), number_format($mob['JEXP']*$expmult));
		//	print PHP_EOL;
			
			if ($mob['MEXP'])
			{
				printf(" | MVP EXP: %s (%02.02f%%)", number_format($expmult*$mob['MEXP']), $mob['ExpPer']);
		//		print PHP_EOL;
			}
			print PHP_EOL;
			if ($mob['aDelay'] < $mob['aMotion'])
				$mob['aDelay'] = $mob['aMotion'];
			
			$int = $mob['INT'];
			printf("   ATK: %s~%s | MATK: %s~%s | DEF: %d+%d | MDEF: %d+(%d+%d) | ASPD: %3.1f", number_format($mob['ATK1']), number_format($mob['ATK2']), number_format($int+intval($int/7*$int/7)), number_format($int+intval($int/5*$int/5)), $mob['DEF'], $mob['VIT'], $mob['MDEF'], $int, intval($mob['VIT']/2), 200.-($mob['aDelay']/20.) );
			//print PHP_EOL;
			
			printf(" | 100%% Hit: %d | 95%% Flee: %d | Critshield: %.2f%%", $mob['AGI']+$mob['LV']+20, $mob['DEX']+$mob['LV']+80-5, $mob['LUK']/5);
			print PHP_EOL;
			
			printf("   Size: %s | Race: %s | Element: %s %d", $sizes[$mob['Scale']], $races[$mob['Race']], $elements[$mob['Element']%10], $mob['Element']/20);
		//	print PHP_EOL;
			
			printf(" | Range: (Atk: %d  View: %d  Chase: %d)", $mob['Range1'], $mob['Range2'], $mob['Range3']);
			print PHP_EOL;
			
			
			
			
			print "   Modes:";
			print PHP_EOL;
			
			$str = "";
			$j=0;
			$mode = $mob['Mode'];
			
			for($i=0; $i < 14; ++$i)
			{
				if ($mode & (1<<$i) )
				{
					if ($i == 11) // MD_AGGRESSIVE and MD_ANGRY
					{
						if ($mode&4)
							continue;
					}
					else
						$str .= "    -".$modes[$i];
						
					
				}
				else
					continue;
					
				if (++$j % 5 == 0)
				{
					print $str;
					print PHP_EOL;
					$str = "";
				}
			}
			if ($j == 0)
				print "None".PHP_EOL;
			else if ($j % 5 != 0)
				print $str.PHP_EOL;
			
			
			
			print "   Drops:".PHP_EOL;
			
			$j=0;
			
			for ($i =0 ; $i < 10; ++$i)
			{
				if ($i == 9)
					$s = "Card";
				else
					$s = $i+1;
				
				if ($mob["Drop{$s}id"])
				{
					$it = get_item_info($mob["Drop{$s}id"]);
					$per = $mob["Drop{$s}per"];
					$item = $it[0];
					
					if ($mob['MEXP'])
						$per *=  1;
					else if ($mob['Mode']&32)
						$per *=  5;
					else
						$per *= 10;
					
					if ($per > 10000)
						$per = 10000;
						
					if ($item['slots'])
						printf("    -%d:(%s[%d]) %02.02f%%", $item['id'], $item['name_japanese'], $item['slots'], $per/100);
					
					else
						printf("    -%d:(%s) %02.02f%%", $item['id'], $item['name_japanese'], $per/100);
						
					if (++$j % 5 == 0)
						print PHP_EOL;
				}				
			}
							
			if ($j == 0)
				print "    None";
			else
			print PHP_EOL;
			
			if ($mob['MEXP'])
			{
				print "   MVP Items:";
				$j = 0;
				
				for ($i=1; $i <= 3; ++$i)
				{
					if($mob["MVP{$i}id"] && ($it = get_item_info($mob["MVP{$i}id"])) )
					{
						$item = $it[0];
						printf("  -%s  %02.02f%%", $item['jName'], $mob["MVP{$i}per"]/100);
					}
				}
				if ($j == 0)
					print "    None.";
				print PHP_EOL;
			}
			
		}
		
	}
	
	function sortByOneKey(array $array, $key, $asc = true) {
		$result = array();
		$values = array();
		
		foreach ($array as $id => $value) 
			$values[$id] = isset($value[$key]) ? $value[$key] : '';
		
		   
		if ($asc) 
			asort($values);
		else 
			arsort($values);
		
		   
		foreach ($values as $key => $value) 
			$result[$key] = $array[$key];
		
		   
		return $result;
	}
	function whodrops_search($val)
	{
		global $dbh;
		$id = intval($val);

		$items = get_item_info($val);
		
		$count = 0;
		foreach ($items as $item)
		{
			
			if (!$item)
			{
				print "Invalid item.";
				return false;
			}
			else
			{
				$query = "SELECT `ID`, `Sprite`, `kName`, `iName`, (`MEXP`>0) as `is_mvp`, (`Mode`&32) as `is_boss`, ";
					
				for($i=1; $i<=9; ++$i) 
					$query .="Drop{$i}id, Drop{$i}per, ";
				
				$query .= "DropCardid, DropCardper ";
				
				
				$query .="FROM `mob_db` WHERE ";
				$j=0;
				
				for($i=1; $i <= 9; ++$i)
					$query .= "Drop{$i}id = :Drop{$i}id OR ";

				$query .= "DropCardid = :DropCardid";

				$query .= " LIMIT 5";
				
				$sth = $dbh->prepare($query);
				
				for($i=1; $i <= 9; ++$i)
					$sth->bindValue("Drop{$i}id", $item['id'], PDO::PARAM_INT);
					
				$sth->bindValue("DropCardid", $item['id'], PDO::PARAM_INT);
				
				$sth->execute();
				
				$mobs = $sth->fetchAll(PDO::FETCH_ASSOC);
				
				$idx = 0;
				$results = array();
				
				foreach($mobs as $mob)
				{
					$per = 0;
					
					if ($item['id'] > 4000 && $item['id'] < 5000)
					{
						if ($mob['DropCardid'] == $item['id'])
						{
							$per = $mob['DropCardper'];
							
							if ( $mob['is_mvp'] )
								$mult = 1;
							else if ( $mob['is_boss'] )
								$mult = 5;
							else
								$mult = 10;
						}
					}
					else
					{
						for($i=1; $i <=9; ++$i)
						{
							if ($mob['Drop'.$i.'id'] == $item['id'])
							{
								$per = $mob['Drop'.$i.'per'];
								
								if ( $mob['is_mvp'] )
									$mult = 1;
								else if ( $mob['is_boss'] )
									$mult = 5;
								else
									$mult = 10;
									
								break;
							}
						}
					}
					
					$per = $per*$mult;
					
					if($per > 10000) 
						$per = 10000;
					
					$results[$idx]['Sprite'] = $mob['Sprite'];
					$results[$idx]['ID'] = $mob['ID'];
					$results[$idx]['iName'] = $mob['iName'];
					$results[$idx]['kName'] = $mob['kName'];
					$results[$idx]['dropRate'] = $per;
					
					++$idx;
				}
				
				$results = sortByOneKey($results, 'dropRate', false);
				
				print "item ".++$count.": '".$item['name_english']."'/'".$item['name_japanese']."' (".$item['id'].")".PHP_EOL;
				$rows = 0;
				foreach ($results as $mob)
				{
						
					printf("  %d) '%s'/'%s'/'%s' (%d)  -  [%.2f%%]", ++$rows, $mob['Sprite'], $mob['kName'], $mob['iName'], $mob['ID'], $mob['dropRate']/100);
					print PHP_EOL;
				}
				
				
				if (!$rows)
					print "  No mob drops found.".PHP_EOL;
			}
		}
	}
	
	
	
	
	$q = trim($_GET['q']);
	$qarr = explode(" ", $q);
	$type = $qarr[0];
	$val = 0;
	
	if (is_numeric($qarr[1]) )
	{
		$val = (int)$qarr[1];
	}
	else
	{
		$qarr[0] = "";
		$val = trim(implode(" ", $qarr));
	}

	//$val == 0 || $val == "" || strpos($val, "%") 
	//print "[[type: ".$type." || v: ". $val. " || test: ".($val === 0 || $val == "" || strpos($val, "%"))."]]".PHP_EOL;
	switch ($type)
	{
		case "help":
			
			print "!ro item [ID/name] - Returns the item info for the given item".PHP_EOL
				 ."!ro mob [ID/name] - Returns the mob info for the given mob".PHP_EOL
				 ."!ro whodrops [ID/name] - Returns a short list of mobs who drop what item for the given item";
			
			
			break;
		
		case "ii": 
		case "iteminfo": 
		case "item":
			if ($val === 0 || $val == "" || strpos($val, "%") )
			{
				print "Invalid search.";
				break;
			}
			else 
			{
				item_search($val);
			}
			
			
			break;
		
		case "mob": 
		case "mobinfo": 
		case "monster";
			
			if ($val === 0 || $val == "" || strpos($val, "%") )
			{
				print "Invalid search.";
				break;
			}
			else 
			{
				mob_search($val);
			}
			
			
			break;
			
		case "whodrops":
			
			if ($val === 0 || $val == "" || strpos($val, "%") )
			{
				print "Invalid search.";
				break;
			}
			else
			{
				whodrops_search($val);
			}
			
			break;
	}

?>

</body>
</html>