<?php
	namespace dao
	{
		class woe_log extends bg_log
		{
			public function __construct($id = null, $killer = true)
			{
				parent::__construct($id, $killer);
			}
		}
	}