<?php

namespace action
{
	class logout extends action_base
	{	
		public function __construct()
		{
			if ( !is_logged_in() )
			{
				$this->set_error_message('You must be logged in to log out!');
				return false;
			}

			$this->status = 'You have been logged out.';
			
			setcookie('uid', 0, 1);
			setcookie('pw', '', 1);

			return $this->good;
		}
	
	
	}
	
}