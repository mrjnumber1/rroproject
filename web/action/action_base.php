<?php

namespace action
{
	class action_base
	{
		protected $good = false;
		protected $error_message = '';
		protected $status = '';
		
		
		public function set_error_message($msg)
		{
			$this->error_message = strval($msg);
			$this->good = false;
			$this->status = '';
		}
		
		public function is_good() { return $this->good; }
		public function get_last_error(){ return strval($this->error_message); }
		public function get_status() { return strval($this->status); }
		public function get_text() { return ( ($this->is_good())?($this->get_status()):($this->get_last_error()) ); } 
	}
}