<?php

	namespace dao
	{
		class mail extends db
		{
			public function __set($name, $value)
			{
				$min = 0;
				$max = \config\constants\number::INT_MAX;

				switch ($name)
				{
					case 'send_id':
					case 'dest_id':
						$min = \config\constants\ragnarok\character::START_CHAR_NUM;
						break;
					case 'send_name':
					case 'dest_name':
						$min = \config\constants\ragnarok\server::NAME_LENGTH_MIN;
						$max = \config\constants\ragnarok\server::NAME_LENGTH;
						break;
					case 'title':
						$max = \config\constants\ragnarok\mail::MAIL_TITLE_LENGTH;
						break;
					case 'message':
						$max = \config\constants\ragnarok\mail::MAIL_BODY_LENGTH;
						break;
					case 'status':
						//$max = \config\constants\ragnarok\enums\mail_type::max_value();
						break;
					case 'zeny':
						$max = \config\constants\ragnarok\character::MAX_ZENY / 2;
						break;


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
						//$max = \config\constants\ragnarok\enums\attribute::max_value();
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


			public function __construct($id = null, $sender = true)
			{
				$this->table = 'mail';

				if ($sender)
				{
					$this->id_column = 'send_id';
					$this->data['dest_id'] = 0;
				}
				else
				{
					$this->id_column = 'dest_id';
					$this->data['send_id'] = 0;
				}

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

				$this->data['send_name']   = '';
				$this->data['dest_name']   = '';
				$this->data['message']     = '';
				$this->data['status']      = \config\constants\ragnarok\mail::MAIL_NEW;
				$this->data['time']        = 0;
				$this->data['title']       = '';
				$this->data['zeny']        = 0;

				if ( !is_null($id) )
				{
					$this->load_by_id( intval($id) );
				}
			}

		}
	}
