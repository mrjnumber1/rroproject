<?php
	namespace dao
	{
		class db
		{
			private static  $dbh = null;

			protected static $cache = array(); // TODO: work this out
			
			protected $new = true;
			
			protected $data = array(); // for table's data
			protected $data_ext = array(); //for JOINED data
			protected $table = '';
			protected $id_column = '';

			
			public function __construct()
			{
                if(self::$dbh == null)
                {
                    self::$dbh = new \PDO(\config\config::$db_dsn,
                        \config\config::$db_user,
                        \config\config::$db_pass,
                        \config\config::$db_options
                    );
                }
				if ( empty($this->id_column) )
                {
					self::log_error('Main Class ID Column undefined', true);
                }
					
				$this->data[$this->id_column]=null;
				//$this->data['uuid']='';
			}

			public function __destruct()
            {
            }
			
			protected function begin()
            {
                return self::$dbh->beginTransaction();
            }
			protected function commit()
            {
                return self::$dbh->commit();
            }
			protected function rollback()
            {
                return self::$dbh->rollBack();
            }
			protected function lastid()
            {
                return self::$dbh->lastInsertId();
            }
			protected function prepare($query)
            {
                return self::$dbh->prepare($query);
            }
			protected function bind(\PDOStatement $statement, $parameter, $value)
            {
                return $statement->bindValue($parameter, $value);
            }
			protected function execute(\PDOStatement $statement)
			{
				$statement->closeCursor();
				return $statement->execute();
			}
			protected function fetch(\PDOStatement $statement)
            {
                return $statement->fetch(PDO::FETCH_ASSOC);
            }
			protected function fetchall(\PDOStatement $statement)
            {
                return $statement->fetchAll(PDO::FETCH_ASSOC);
            }
			
			protected function load_by_id($id)
			{
				$this->load_by_query( "SELECT * FROM `{$this->_table}` WHERE `{$this->id_column}`=:{$this->id_column}", array( $this->id_column => $id) );
			}


			protected function load_by_query($query, array $vars)
			{
				$statement = $this->prepare($query);
				
				foreach ($vars as $column => $value)
                {
					if( !$this->bind($statement,":$column",$value) )
                    {
						throw new CannotBindException();
                    }
                }
						
				if ( !$this->execute($statement) )
                {
					throw new InvalidQueryException();
                }
				
				$row = $this->fetch($statement);
				if (!$row)
                {
					throw new NotFoundException();
                }
					
				$this->read_row($row);
				
				$this->new = false;
			}
			
			protected function read_row($row)
            {
                foreach($row as $column => $value)
                {
                    $this->data[$column] = $value;
                }
            }
			
			public function save()
			{
				$updates = array();

				foreach ($this->data as $column => $value)
                {
					if ($column != $this->id_column)
                    {
						$updates[] = "`$column`=:$column" ;
                    }
                }
						
				$sets = implode(',',$updates);

				if ($this->new)
                {
					$query = "INSERT INTO `{$this->table}` SET $sets; SELECT LAST_INSERT_ID() AS `{$this->id_column}`;";
                }
				else
                {
					$query = "UPDATE `{$this->table}` SET $sets WHERE `{$this->id_column}`=?{$this->id_column}";
                }

				$statement = $this->prepare($query);
				
				foreach($this->data as $column => $value)
                {
					if( !$this->bind($statement,":$column",$value) )
                    {
						throw new CannotBindException();
                    }
                }

				$this->execute($statement);
				
				if($this->new)
				{
					$return = $this->fetch($statement);
					$this->data[$this->id_column] = $return[$this->id_column];
					$this->new = false;
				}

                return true;
            }

            public static function log_error($error, $die = false)
            {
                $errdb = new \PDO(\config\config::$db_dsn,
                        \config\config::$db_user,
                        \config\config::$db_pass,
                        \config\config::$db_options
                        );

                $error .= ' in '. __FILE__ .' on line '. __LINE__;
                $error  = \lib\string\left($error, 2048);

                $stmt = $errdb->prepare('INSERT INTO `www_log_error` (`uuid`,`hit`,`error`) VALUES (:uuid,:hit,:error);');
                $stmt->bindValue(':uuid', \lib\uuid::v4() );
                $stmt->bindValue(':hit', null);
                $stmt->bindValue(':error', $error);


                $stmt->execute();

                if($die)
                {
                    trigger_error( \lib\debug\print_s($error . '\n' .\lib\debug\get_backtrace() ), E_USER_NOTICE);
                    die();
                }
            }

			protected function fetch_rows($rows)
            {

            }

			
			public function __set($name, $value)
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
			
			public function __isset($name)
            {
                return isset($this->data[$name]);
            }
			public function __unset($name)
            {
                unset($this->data[$name]);
            }

        }
		
		class CannotBindException extends Exception
        {
        }
		class InvalidQueryException extends Exception
        {
        }
		class NotFoundException extends Exception
        {
        }
	}