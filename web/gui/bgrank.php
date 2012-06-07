<?php

namespace gui
{
	class bgrank extends bgwoe_rank
	{


		public function __construct()
		{
			$this->table = 'bg_stats';
			$this->title = 'BG Rankings';
		}


	}
}