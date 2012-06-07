<?php

namespace action
{
	class register extends action_base
	{	
		private function create_salt()
		{
			$str = md5(uniqid(time(), true));
			return substr($str, 8, 15);
		}

		private function is_valid_username($name)
		{
			global $dbh;
			$name = strval($name);
			
			if ( strlen($name) > 23)
			{
				// can only be done by packetting.. log this
				$this->set_error_message('The username you have entered is far too long!');
				return false;
			}
			
			if ( !ctype_alnum($name) )
			{
				$this->set_error_message('Sorry, but only alpha-numeric usernames are accepted');
				return false;
			}
			
			try
			{
				$query = 'SELECT `member_id` FROM `members` WHERE `name`=:name';
				$sth = $dbh->prepare($query);
				$sth->bindValue(':name', $name);
				$sth->execute();
				
			
				if ($sth->rowCount() > 0)
				{
					$this->set_error_message('Sorry, this username is taken.');
					return false;
				}
			}
			catch (\PDOException $e)
			{
				$this->set_error_message($e->getMessage());
				return false;
			}
			
			return true;
		}
		
		private function is_valid_password($pass1, $pass2)
		{
			$pass1 = strval($pass1);
			$pass2 = strval($pass2);
			
			if ($pass1 != $pass2)
			{	
				$this->set_error_message('The passwords you entered do not match.');
				return false;
			}
			
			// all good!
			return true;
		}
		
		private function insert_member($user, $pass, $salt)
		{
			global $dbh;
			$user = strval($user);
			$pass = strval($pass);
			$salt = strval($salt);
			
			try
			{
				$query = 'INSERT INTO `members` (`name`, `mpass`, `salt`) VALUES ( :name, :mpass, :salt)';
				
				$sth = $dbh->prepare($query);
				$sth->bindValue(':name', $user);
				$sth->bindValue(':mpass', $pass);
				$sth->bindValue(':salt', $salt);
				

				return $sth->execute();
			}
			catch (\PDOException $e)
			{
				$this->set_error_message($e->getMessage());
				return false;
			}
			
			//return false;
		}
		
		public function __construct()
		{
			if ( is_logged_in() )
			{
				$this->set_error_message('You are already logged in!');
				return false;
			}

			$user = $_POST['reg_username'];
			$pass1 = sha1(md5($_POST['reg_pass1']));
			$pass2 = sha1(md5($_POST['reg_pass2']));
			$len = strlen($_POST['reg_pass1']);
			
			if ( $len > 23 || $len < 10) // technically should be in is_valid_password, but i don't want PW strings showing on the stack
			{
				$this->set_error_message('Sorry, passwords must be between 10 and 23 characters');
			}
			else if ( $this->is_valid_username($user) && $this->is_valid_password($pass1, $pass2) )
			{
				$salt = $this->create_salt();
				
				$pw_hash = @@@@@@@@@@SALTING METHOD GOES HERE. MAKE SURE IT FULLY MATCHES FROM login.php.tpl@@@@@@@@@	
				
				if ( $this->insert_member($user, $pw_hash, $salt) )
					$this->good = true;
			}
			
			
			return $this->good;
		}
	}
}