<?php
namespace gui
{
	class indexpage extends gui_base
	{
		public function build()
		{
			$str = 'Ah! If you\'re seeing this it should be obvious that things are still being set up'
				.'. Head over to the <a href="http://board.karma-ro.com/">forums</a> until we\'re done.'
				.'<br> Remember, June 6th!';
			return $str;
		}
		public function __construct()
		{
			return true;
		}
	}

}