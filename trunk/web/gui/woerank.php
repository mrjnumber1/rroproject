<?php

namespace gui
{
	class woerank extends bgwoe_rank
	{

		public function __construct()
		{
			$this->table = 'woe_stats';
			$this->title = 'WoE Rankings';
		}
	}
}