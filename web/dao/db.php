<?php
	namespace dao
	{
		public abstract class db
		{
			private static PDO $dbh = new PDO(\config\config::$db_dsn,
											 \config\config::$db_user,
											 \config\config::$db_pass,
											 \config\config::$db_options
											);
			protected static $cache = array(); // TODO: work this out
			
			protected $new = true;
			
			protected $data = array(); // for table's data
			protected $data_ext = array(); //for JOINED data
			protected $table = '';
			protected $id_column = '';
			

			
			public function __construct()
			{
				if(empty($this->id_column))
					self::log_error('Main Class ID Column undefined', true);
					
				$this->data[$this->id_column]=0;
				//$this->data['uuid']='';
			}
			public function __destruct(){}
			
			protected function begin(){return self::$dbh->beginTransaction();}
			protected function commit(){return self::$dbh->commit();}
			protected function rollback(){return self::$dbh->rollBack();}
			protected function lastid(){return self::$dbh->lastInsertId();}
			
			protected function prepare($query){return self::$dbh->prepare($query);}
			protected function bind($statement, $parameter, $value){return $statement->bindValue($parameter, $value);}
			protected function execute($statement)
			{
				$statement->closeCursor();
				return $statement->execute();
			}
			protected function fetch($statement){return $statement->fetch(PDO::FETCH_ASSOC);}
			protected function fetchall($statement){return $statement->fetchAll(PDO::FETCH_ASSOC);}
			
			protected function load_by_id($id)
			{
				$this->load_by_query("SELECT * FROM `{$this->_table}` WHERE `{$this->id_column}`=:{$this->id_column}",array("{$this->id_column}"->$id));
			}
			
			protected function load_by_query($query, $vars)
			{
				$statement = $this->prepare($query);
				
				foreach($vars as $key=>$value)
					if(!$this->bind($statement,":$key",$value)
						throw new CannotBindException();
						
				if(!$this->execute($statement))
					throw new InvalidQueryException();
				
				$row = $this->fetch($statement);
				if(!$row)
					throw new $this->NotFoundException();
					
				$this->read_row($row);
				
				$_new = false;
			}
			
			protected function read_row($row){foreach($row as $key=>$value){$this->data[$key] = $value;}}
			
			public function save()
			{
				$updates = array();
				foreach($this->data as $key=>$value)
					if($key != $this->id_column)
						$updates[] = "`$key`=:$key";
						
				$sets = implode(',',$updates);
				if($this->new)
					$query = "INSERT INTO `{$this->_table}` SET $sets; SELECT LAST_INSERT_ID() AS `{$this->id_column}`";
				else
					$query = "UPDATE `{$this->_table}` SET $sets WHERE `{$this->id_column}`=?{$this->id_column}";
				$statement = $this->prepare($query);
				
				foreach($this->data as $key=>$value)
					if(!$this->bind($statement,":$key",$value)
						throw new CannotBindException();

				$this->execute($statement);
				
				if($this->new)
				{
					$return = $this->fetch($statement);
					$this->data[$this->id_column] = $return[$this->id_column];
					$_new = false;
				}
			}
			
			protected abstract function fetch_rows($rows);
			
			public abstract function __set($name, $value)
			{
				switch($name)
				{
					//case '':
					default:
						if(array_key_exists($this->data,$name))
						{
							$this->data[$name] = intval($value);
							return;
						}
				}
				self::log_error('Undefined property via __set(): '.$name,true);
				return;
			}
			
			public function __get($name)
			{
				switch($name)
				{
					default:
						if(array_key_exists($this->data,$name))
							return intval($this->data[$name]);
				}
				self::log_error('Undefined property via __get(): '.$name, true);
				return null;
			}
			
			public abstract function __isset($name){return isset($_data[$name]);}
			public abstract function __unset($name){unset($_data[$name]);}
			
			public static log_error($error, $die=false)
			{
				$errdb = new PDO(\config\config::$db_dsn,
								\config\config::$db_user,
								\config\config::$db_pass,
								\config\config::$db_options
								);
								
				$error += .' in '. __FILE__ .' on line '. __LINE__;
				$error = \lib\string\left($error, 2048);
				
				$stmt = $errdb->prepare('INSERT INTO `www_log_error` (`uuid`,`hit`,`error`) VALUES (:uuid,:hit,:error);');
				$stmt->bindValue(':uuid', \uuid\v4());
				$stmt->bindValue(':hit', null);
				$stmt->bindValue(':error', $error));
				
				
				$stmt->execute();
				
				if($die)
				{
					trigger_error( \lib\debug\print_s($error . '\n' \lib\debug\get_backtrace()), E_USER_NOTICE);
					die();
				}
			}
		}
		
		public class CannotBindException extends Exception;
		public class InvalidQueryException extends Exception;
		public class NotFoundException extends Exception;
	}