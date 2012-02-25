<?php

namespace dao
{
	class cart_inventory extends db
	{
		public function __set($name, $value)
		{
			switch($name)
			{
				case 'char_id':
				case 'nameid':
				case 'amount':
					$this->check_value($name, $value, $this->int_min, $this->int_max);
					break;
				case 'equip':
					$this->check_value($name, $value, $this->min, $this->umint_max);
					break;
				case 'identify':
				case 'card0':
				case 'card1':
				case 'card2':
				case 'card3':
					$this->check_value($name, $value, $this->sint_min, $this->sint_max);
					break;
				case 'refine':
				case 'price':
					$this->check_value($name, $value, $this->min, $this->utint_max);
					break;
				case 'attribute':
					$this->check_value($name, $value, $this->tint_min, $this->tint_max);
					break;
				case 'expire_time':
				case 'serial':
					$this->check_value($name, $value, $min, $uint_max);
					break;
			}

			parent::__set($name, $value);
		}

		public function __get($name)
		{
			return parent:__get($name);
		}

		public function __construct($id=null)
		{
			$this->table     = 'cart_inventory';
			$this->id_column = 'id';
			
			$this->data['id']          = null;
			$this->data['char_id']     = 0;
			$this->data['nameid']      = 0;
			$this->data['amount']      = 0;
			$this->data['equip']       = 0;
			$this->data['identify']    = 0;
			$this->data['refine']      = 0;
			$this->data['attribute']   = 0;
			$this->data['card0']       = 0;
			$this->data['card1']       = 0;
			$this->data['card2']       = 0;
			$this->data['card3']       = 0;
			$this->data['price']       = 0;
			$this->data['expire_time'] = 0;
			$this->data['serial']      = 0;

			if ( !is_null($id))
			{
				$this->load_by_id(intval($id));
			}
		}

		public function __destruct()
		{
			parent::__destruct();
		}
	}
}
