<?php
	namespace config\constants\ragnarok
	{
		// to determine whether it belongs in one of the specifics or the generic (server)
		// consider, does this apply to multiple of the specifics? if yes, put it in server!
		class character
		{
			const MAX_CHARS = 9;
			const START_CHAR_NUM = 150000;
			const MAX_ZENY = 1000000000;
			const MAX_FAME = 1000000000;
			const MAX_HAIR_STYLE = 45;
			const MAX_HAIR_COLOR = 263;
			const MAX_CLOTH_COLOR = 553;
			const MAX_LEVEL = 99;
			const MAX_STAT = 99;
		}
		
		class account
		{
			const START_ACCOUNT_NUM = 2000000;
			const END_ACCOUNT_NUM = 1000000000;
		}
			
			
		class map
		{
			const MAP_NAME_LENGTH = 11;
			const MAP_NAME_LENGTH_EXT = 15; // NAME_LENGTH + strlen('.gat');
		}

		class server
		{
			const NAME_LENGTH = 23;
			const MAX_FAME_LIST = 10;
		}
    }

    namespace config\constants\ragnarok\enums
    {
        function dummy()
        {

        }
	}