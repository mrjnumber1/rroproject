<?php

namespace action
{
	class login extends action_base
	{	
		private function check_login_details($user, $pw_hash)
		{
			global $dbh;
			$user = strval($user);
			$pw_hash = strval($pw_hash);
			
			try
			{
				$query = 'SELECT `member_id`, `mpass`, `salt` FROM `members` WHERE `name` = :name';

				$sth = $dbh->prepare($query);
				$sth->bindValue(':name', $user);
				$sth->execute();
				
				if ( ($data = $sth->fetch(\PDO::FETCH_ASSOC)) == false )
				{
					// this user doesn't exist
					$this->set_error_message('This user does not exist!');
					return false;
				}
				
				$pw_hash = FULL HASH/SALTING METHOD GOES HERE@@@@@@@@@@@@@@@
				if($pw_hash != $data['mpass'])
				{
					$this->set_error_message('You have entered an invalid password.');
					return false;
				}
				
				return $data['member_id'];
			}
			catch (\PDOException $e)
			{
				$this->set_error_message($e->getMessage());
				return false;
			}
		}
		
		public function __construct()
		{
			if ( is_logged_in() )
			{
				$this->set_error_message('You are already logged in!');
				return false;
			}
			
			$user = trim($_POST['userlogin']);
			$pw	= $_POST['userpw'];
			$pw_hash = @@@@@@@@@@PARTIALLY HASH THIS AT LEAST SO IT ISN'T EXPOSED TO THE STACKTRACE@@@@@@@@@2
			
			$id = $this->check_login_details($user, $pw_hash);

			if ( $id !== false )
			{
				//VALIDATE USER COOKIES OR SOMETHING!!
				$this->good = true;
				$this->status = 'Login details confirmed. Welcome back, '.htmlspecialchars($user, ENT_QUOTES, 'UTF-8').'!'; //technically not necessary, but hey!
			}
			
			return $this->good;
		}
	
	
	}
	
}