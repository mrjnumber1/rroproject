<?php

namespace dao
{
	class char_log extends db
	{
	 	public function __set($name, $value)
		{
			switch($name)
			{
				case 'time':
					$this->check_value($name, $value, 0, 20);
					break;
				case 'char_msg':
					$this->check_value($name, $value, 0, 255);
					break;
				case 'name':
					$this->check_value($name, $value, 0, 23);
					break;
				case 'account_id':
				case 'hair_color':
					$this->check_value($name, $value, $this->int_min, $this->int_max);
					break;
				case 'str':
				case 'agi':
				case 'vit':
				case 'int':
				case 'dex':
				case 'luk':
					$this->check_value($name, $value, $this->min, $this->uint_max);
					break;
			}

			parent::__set($name, $value);
		}

		public function __construct($id=null)
		{
			$this->table     = 'char_log';
			$this->id_column = ''; //TODO: id column is not defined in the table

			$this->data['time']       = 0;
			$this->data['char_msg']   = '';
			$this->data['account_id'] = 0;
			$this->data['char_num']   = 0;
			$this->data['name']       = '';
			$this->data['str']        = 0;
			$this->data['agi']        = 0;
			$this->data['vit']        = 0;
			$this->data['int']        = 0;
			$this->data['dex']        = 0;
			$this->data['luk']        = 0;
			$this->data['hair']       = 0;
			$this->data['hair_color'] = 0;
		}
	} 
}
