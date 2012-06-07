<?php
	error_reporting(E_ALL);
	define('START', microtime(true));
	ob_start();

	function is_logged_in()
	{
		global $dbh;
		//var_dump($_COOKIE);
		$mid = isset($_COOKIE['uid'])?$_COOKIE['uid']:0;
		$pw = isset($_COOKIE['pw'])?$_COOKIE['pw']:'';

		if (!$mid || !$pw)
			return false;

		try
		{
			$query = 'SELECT `member_id` FROM `members` WHERE `member_id`=:member_id AND `mpass`=:mpass';
			$sth = $dbh->prepare($query);

			$sth->bindValue(':member_id', $mid);
			$sth->bindValue(':mpass', $pw);

			$sth->execute();

			return (count($sth->fetch(\PDO::FETCH_ASSOC))==1);
		}
		catch (\PDOException $e)
		{
			return false;
		}
	}


	// we do this so that people can't force a reload by adding gets besides p/args
	$arguments = array();
	foreach($_GET as $key=>$val)
		if($key === 'p')
			$arguments[$key] = $val;
		elseif($key === 'arg')
			$arguments[$key] = $val;


	$request_str = '?';

	$i=0;

	foreach($arguments as $arg=>$val)
	{
		if($i)
			$request_str .= '&amp;'.$arg.'='.$val;
		else
			$request_str .= $arg.'-'.$val;
		++$i;
	}
	if ( is_logged_in())
	{
		$request_str .= '&amp;uid='.$_COOKIE['uid'];
	}

	$cache_folder = '..'.DIRECTORY_SEPARATOR.'jcp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'';
	$cache_expire = 10;
	$cache_file = $cache_folder.sha1($request_str).'.html';

	$last_cache = 0;

	if ( file_exists($cache_file) )
	{
		$last_cache = filemtime($cache_file);
	}
	if ( (time() - $last_cache) < $cache_expire && !isset($_GET['action']) ) // do not cache action pages for obvious reasons!
	{
		//$finfo = new \finfo();
		$header = 'Content-Type:';
		//$header = 'Content-Type:'.$finfo->file($cache_file, FILEINFO_MIME).';';

		if (stripos($cache_file, '.htm') > 0)
		{
			$header = 'Content-Type: text/html; charset=utf-8';
		}
		else if (stripos($cache_file, '.png') > 0)
		{
			$header = 'Content-Type: image/png;';
		}
		header($header);
		readfile($cache_file);
		exit;
	}

	date_default_timezone_set('America/New_York');
	$date = new \DateTime();

	include '../jcp/config/loader.php';
	
	$base_loader =  new \config\loader('', '');
	$base_loader->register();

	$dbh = null;
	try
	{
		$dbh = new \PDO(\config\config::$db_dsn, \config\config::$db_user, \config\config::$db_pass, \config\config::$db_options);
	}
	catch (\PDOException $e)
	{
		$page_data = $e->getMessage();
	}

	
	$page_data = '';
	$title = 'Home';
	$show_page = true;
	$header = 'Content-Type: text/html; charset=utf-8';
	$page = isset($_GET['p'])?$_GET['p']:'indexpage';
	$act = isset($_GET['action'])?$_GET['action']:null;
	
	if ( count($_POST) )
	{
		if ( ctype_lower($act) )
		{
			if ( !file_exists("../jcp/action/{$act}.php") )
			{
				$page_data = "Page not found";
			}
			else
			{
				$page = null;
				$class = "\action\\{$act}";
				$status = new $class();
				$page_data = $status->get_text();
			}
		}
	}
	
	if ($page !== null)
	{
		if ( ctype_lower($page) )
		{
			if ( !file_exists("../jcp/gui/{$page}.php") )
			{
				$page_data = "Page not found";
			}
			else
			{
				
				$args = array();
				if ( isset($_GET['arg']) )
				{
					$arr = explode(';', $_GET['arg']);
					$size = count($arr);
					
					if ($size%2 != 0)
						$size -= 1;
						
					for ($i=0; $i < $size; $i+=2)
						$args[$arr[$i]] = $arr[$i+1];
					
				}

				$class = "\gui\\{$page}";
				$status = new $class($args);

				//var_dump($status);
				$page_data = $status->build();
				$title = $status->get_title();
				$header = $status->get_header();
				$page_heading = $status->get_page_heading();
				$show_page = $status->render_page();
			}
		}
	}
	header($header);
	if (!$show_page)
	{
		die();
	}

	function get_server_status()
	{
		$scache_dir = '..'.DIRECTORY_SEPARATOR.'jcp'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'';
		$scache_file = $scache_dir .'sstatus.tmp';
		$status = 0;

		if ( !file_exists($scache_file) )
		{
			file_put_contents($scache_file, "0");
		}

		if ( filemtime($scache_file) < time() - 30)
		{
			for ($i=0; $i<3; ++$i)
				if ( $s = @fsockopen(\config\config::$ro_ip[$i], \config\config::$ro_port[$i], $_, $_, .01) )
				{
					fclose($s);
					$status += 1 << $i;
				}

			file_put_contents($scache_file, $status);
		}
		else
		{
			$status = file_get_contents($scache_file);
		}

		$str = '';

		for ($i=0; $i < 3; ++$i)
		{
			if($status&1)
				$str .= "<img src=\"images/arrow_up.png\" alt=\"UP!\" />";
			else
				$str .= "<img src=\"images/arrow_down.png\" alt=\"DOWN!\" />";
			$status >>= 1;
		}

		return $str;
	}

	function get_player_counts()
	{
		global $dbh;
		$counts = array();
		try
		{
			$query = "SELECT count(*) AS `amt` FROM `char` WHERE `online`='1'";
			$sth = $dbh->prepare($query);
			$sth->execute();
			$data = $sth->fetch(\PDO::FETCH_ASSOC);

			$counts['players'] = intval($data['amt']);
			$counts['vendors'] = 0;

			$query = 'SELECT `value` FROM `mapreg` WHERE `varname`=\'$BLU_size\'';
			$sth = $dbh->prepare($query);
			$sth->execute();
			$data = $sth->fetch(\PDO::FETCH_ASSOC);

			$counts['blu_size'] = intval($data['value']);

			$query = 'SELECT `value` FROM `mapreg` WHERE `varname`=\'$RED_size\'';
			$sth = $dbh->prepare($query);
			$sth->execute();
			$data = $sth->fetch(\PDO::FETCH_ASSOC);

			$counts['red_size'] = intval($data['value']);
		}
		catch (\PDOException $e)
		{
			print $e->getMessage();
		}
	}

	$count = get_player_counts();

?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<title>Karma-RO - <?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="styles/1.css" />
</head>
<body>
<div id="headertop" >
</div>

<div id="header">

	<div id="loginbox"> 

		<div id="logincontainer">
			<form action="?action=login" method="post">
				<label><input name="server" value="Karma-RO" type="hidden" /></label>
				<label><input type="submit" value="" id="loginbutton" /></label>
				<label><input name="userlogin" maxlength="23" type="text" class="loginfield"/></label>
				<label><input name="userpw" maxlength="23" type="password" class="loginfield" /></label>
			</form>
		</div>
	</div> 
	
	<br /><br /><br />
	<div id="statusbox">
	
		<div id="arrowcontainer">
			<?php echo get_server_status();?>
		</div>
		<div id="timecontainer">
			<p><?php echo $date->format('H:i:s');?></p>
		</div>
	</div>
</div>

<div id="headerbot">
</div>

<div id="page">

	<div id="navmenu">
		<div class="navheader" id="mainheader">
		</div>
		<ul class ="nav">
			<li><a href=".">Home</a></li>
			<li><a href="http://board.karma-ro.com">Forum</a></li>
			<li><a href="http://board.karma-ro.com/index.php/topic,3.0.html">IRC</a></li>
		</ul>
		<div class="navheader" id="infoheader">

		</div>
		<ul class ="nav">
			<li><a href="http://board.karma-ro.com/index.php/board,4.0.html">Server Info</a></li>
			<li><a href="http://board.karma-ro.com/index.php/topic,32.0.html">Staff</a></li>
			<li><a href="http://board.karma-ro.com/index.php/topic,37.0.html">Download</a></li>
			<li><a href="?p=register">Register</a></li>
			<li><a href="?p=donate">Donation</a></li>
		</ul>

		<div class="navheader" id="rankheader">

		</div>
		<ul class ="nav">
			<li><a href="?p=bgrank">Battlegrounds</a></li>
			<li><a href="?p=charlist">Characters</a></li>
			<li><a href="?p=woerank">War of Emperium</a></li>
			<li><a href="?p=guildlist">Guild</a></li>
			<li><a href="?p=castlelist">Castle Status</a></li>
		</ul>

		<div class="navheader" id="refheader">

		</div>
		<ul class ="nav">
			<li><span>Mission DB</span></li>
			<li><span>EXP Tables</span></li>
			<li><span>Item DB</span></li>
			<li><span>Mob DB</span></li>
			<li><span>Refine Success</span></li>
			<li><span>Size Modifiers</span></li>
			<li><span>Item Groups</span></li>
			<li><span>Refines</span></li>
		</ul>
	</div>


	<div id="maincontent">
<?php
		if ( isset($page_heading) )
		{
			echo "<h3>{$page_heading}</h3><hr />";
		}
?>

		<?php echo $page_data;?>
	</div>
	


	<div id="playerstatus">

		<div id="acoheader">
		</div>
		<div id="pcbox">
			<?php echo $count['players'];?><br/>
			<?php echo $count['vendors'];?>
		</div>
		
		
		<div id="smithheader">
		</div>
		<div id="cqbox">
			<br/><br/>
			<span id="redcount"><?php echo $count['red_size'];?></span>
			<span id="blucount"><?php echo $count['blu_size'];?></span>
		<!--	<img id="redcount" src="images/red.png" alt="RED: " />
			<img id="blucount" src="images/blu.png" alt="BLU: "/> -->
		</div>
		
		
		<div id="swordmanheader">
		</div>
		<div id="woebox">
			<br />
			<p>167:59:59</p>	
		</div>

	</div>


</div>

</body>
</html>
<?php
	//$now = microtime(true) - START;
	//print $now;
	$dbh = null;

	if (!isset($_GET['action']))
		file_put_contents($cache_file, ob_get_contents());
	ob_end_flush();

