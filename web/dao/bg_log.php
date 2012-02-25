<?php

namespace dao
{
	class bg_log
	{
		public function __set($name, $value)
		{
			$min = 0;
			$max = 0xFFFFFFFF;

			switch($name)
			{
				case 'id':
				case 'killer_id':
				case 'killed_id':
				case 'skill':
					$this->check_value($name, $value, $this->int_min, $this->int_max);
					break;
				case 'time':
					$this->check_value($name, $value, 0, 20);
					break;
				case 'killer':
				case 'killed':
					$this->check_value($name, $value, 0, 25);
					break;
				case 'map':
					$this->check_value($name, $value, 0, 11);
					break;
			}

			parent:__set($name, $value);
		}

		public function __construct($id=null)
		{
			$this->table     = 'bg_log';
			$this->id_column = 'id';

			$this->data['id']        = null;
			$this->data['time']      = '0000-00-00 00:00:00';
			$this->data['killer']    = '';
			$this->data['killer_id'] = 0;
			$this->data['killed']    = '';
			$this->data['killed_id'] = 0;
			$this->data['map']       = '';
			$this->data['skill']     = 0;

			if ( !is_null($id))
			{
				$this->load_by_id(intval($id));
			}
		}
	}
}
