<?php

namespace dao
{
    class auction extends db
	{
	    public function __set($name, $value)
		{
			$min = 0;
			$max = \config\constants\number::INT_MAX;

			switch ($name)
			{
				case 'seller_name':
				case 'buyer_name':
					$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
					$max = \config\constants\ragnarok\server::NAME_LENGTH;
					break;
				case 'item_name':
					$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
					$max = 50;
					break;
				case 'seller_id':
				case 'buyer_id':
					$min = \config\constants\ragnarok\character::START_CHAR_NUM;
					break;
				case 'price':
					$max = \config\constants\ragnarok\character::MAX_ZENY / 2 - 1; // defined in battle_config
					break;
				case 'buy_now':
					$max = \config\constants\ragnarok\character::MAX_ZENY / 2;
					break;
				case 'hours':
					$min = 1;
					$max = 48;
					break;
				case 'timestamp':
					$max = \lib\timer\current_timestamp();
					break;
				case 'nameid': //these are all item ids
					$min = \config\constants\ragnarok\item::START_ITEMID;
					$max = \config\constants\ragnarok\item::MAX_ITEMID;
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

			if ( ! $this->set_value($name, $value, $min, $max) )
			{
				parent::__set($name, $value);
			}
		}

		public function __construct($id = null)
		{
		    $this->table     = 'auction';
			$this->id_column = 'auction_id';

			parent::__construct();

			$this->data['seller_id']   = 0;
			$this->data['seller_name'] = '';
			$this->data['buyer_id']    = 0;
			$this->data['buyer_name']  = '';
			$this->data['price']       = 0;
			$this->data['buy_now']     = 0;
			$this->data['hours']       = 0;
			$this->data['timestamp']   = 0;
			$this->data['item_name']   = '';
			$this->data['nameid']      = 0;
			$this->data['type']        = 0;
			$this->data['refine']      = 0;
			$this->data['attribute']   = 0;
			$this->data['card0']       = 0;
			$this->data['card1']       = 0;
			$this->data['card2']       = 0;
			$this->data['card3']       = 0;
			$this->data['serial']      = 0;

			if ( !is_null($id) )
			{
				$this->load_by_id( intval($id) );
			}
		}

	}
}

