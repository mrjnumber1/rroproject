<?php

namespace dao
{
	class charlog extends db
	{
	 	public function __set($name, $value)
		{
			$min = 0;
			$max = \config\constants\number::INT_MAX;

			switch ($name)
			{
				case 'time':
					$max = \lib\timer\current_timestamp();
					break;
				case 'char_msg':
					$min = 1; //no empty messages
					$max = 255;
					break;
				case 'name':
					$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
					$max = \config\constants\ragnarok\server::NAME_LENGTH;
					break;
				case 'account_id':
					$min = \config\constants\ragnarok\account::START_ACCOUNT_NUM;
					$max = \config\constants\ragnarok\account::END_ACCOUNT_NUM;
					break;
				case 'hair_color':
					$max = \config\constants\ragnarok\character::MAX_HAIR_COLOR;
					break;
				case 'str':
				case 'agi':
				case 'vit':
				case 'int':
				case 'dex':
				case 'luk':
					$min = 1;
					$max = \config\constants\ragnarok\character::MAX_STAT;
					break;
			}

			if ( ! $this->set_value($name, $value, $min, $max) )
			{
				parent::__set($name, $value);
			}
		}

		public function __construct($id = null)
		{
			$this->table     = 'char_log';
			$this->id_column = 'account_id'; // We sort these by accoutn id;

			parent::__construct();

			$this->data['time']       = 0;
			$this->data['char_msg']   = '';
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

			if ( !is_null($id) )
			{
				$this->load_by_id( intval($id) );
			}
		}
	} 
}
