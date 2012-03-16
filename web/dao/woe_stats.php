<?php
	namespace dao
	{
		class woe_stats extends bg_stats
		{
			public function __construct($id = null, $killer = true)
			{
				parent::__construct($id, $killer);
			}
		}
	}