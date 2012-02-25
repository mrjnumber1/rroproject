<php

namespace dao
{
    class auction extends db
	{
	    public function __set($name, $value)
		{
			$min = 0;
			$max = 0xFFFFFFFF;

			switch($name)
			{
				case 'seller_name':
				case 'buyer_name':
					$this->check_value($name, $value, $min, 30);
					break;
				case 'item_name':
					$this->check_value($name, $value, $min, 50);
					break;
				case 'seller_id':
				case 'buyer_id':
				case 'price':
				case 'buy_now':
				case 'timestamp':
				case 'nameid':
				case 'serial':
					$this->check_value($name, $value, $min, $max);
					break;
				case 'hours':
				case 'type':
				case 'card0':
				case 'card1':
				case 'card2':
				case 'card3':
					$this->check_value($name, $value, $min, 32767); // smallint
					break;
				case 'refine':
				case 'attribute':
					$this->check_value($name, $value, $min, 255); // tinyint
				break;
			}

			parent:__set($name, $value);
		}

		public function __get($name)
		{
			return parent::__get($name);
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
			$this->data['nameid']      = 0;
			$this->data['item_name']   = '';
			$this->data['type']        = 0;
			$this->data['refine']      = 0;
			$this->data['attribute']   = 0;
			$this->data['card0']       = 0;
			$this->data['card1']       = 0;
			$this->data['card2']       = 0;
			$this->data['card3']       = 0;
			$this->data['serial']      = 0;

			if (! is_null($id))
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

