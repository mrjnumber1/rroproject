<?php

namespace dao
{
	class cart_inventory extends db
	{
		public function __set($name, $value)
		{
			$min = 0;
			$max = \config\constants\number::INT_MAX;

			switch ($name)
			{
				case 'char_id':
					$min = \config\constants\ragnarok\character::START_CHAR_NUM;
				case 'nameid':
					$min = \config\constants\ragnarok\item::START_ITEMID;
					$max = \config\constants\ragnarok\item::MAX_ITEMID;
					break;
				case 'amount':
					$max = \config\constants\ragnarok\server::MAX_AMOUNT;
					break;
				case 'card0':
				case 'card1':
				case 'card2':
				case 'card3':
					// 0 = no card
					$max = \config\constants\ragnarok\item::MAX_ITEMID_CARD;
					break;
				case 'refine':
					$max = \config\constants\ragnarok\item::MAX_REFINE;
					break;
				case 'attribute':
					$max = \config\constants\ragnarok\item::MAX_ATTRIBUTE;
					break;
				case 'type':
					//$max = \config\constants\ragnarok\enums\item_type::max_value();
					break;
				case 'serial':
					$max = \config\constants\number::UINT_MAX;
					break;
			}

			if ( !$this->set_value($name, $value, $min, $max) )
			{
				parent::__set($name, $value);
			}
		}


		public function __construct($id = null)
		{
			$this->table     = 'cart_inventory';
			$this->id_column = 'char_id';
			
			parent::__construct();

			$this->data['id']     = 0;
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

			if ( !is_null($id) )
			{
				$this->load_by_id( intval($id) );
			}
		}

	}
}
