<?php

	namespace config\constants\ragnarok
	{
		// to determine whether it belongs in one of the specifics or the generic (server)
		// consider, does this apply to multiple of the specifics? if yes, put it in server!

		class account
		{
			const START_ACCOUNT_NUM = 2000000;
			const END_ACCOUNT_NUM = 1000000000;

			const MAX_STORAGE = 600;
			const MAX_CHARS = 9;
		}

		class character
		{
			const MAX_CHARS = 9;
			const START_CHAR_NUM = 150000;
			const MAX_ZENY = 1000000000;
			const MAX_CASH = 1000000000;
			const MAX_FAME = 1000000000;
			const MAX_HAIR_STYLE = 45;
			const MAX_HAIR_COLOR = 263;
			const MAX_CLOTH_COLOR = 553;
			const MAX_LEVEL = 99;
			const MAX_STAT = 99;

			const MAX_INVENTORY = 100;
			const MAX_CART      = 100;

			const MAX_COOK_MASTERY = 1999;
		}

		class friend
		{
			const MAX_FRIENDS = 40;
		}

		class guild
		{
			const MAX_GUILDEXPULSION = 32;
			const MAX_GUILDALLIANCE  = 16;
			const MAX_GUILDSKILL     = 15;
			const MAX_GUILDCASTLE    = 34;
			const MAX_GUILDLEVEL     = 50;
			const MAX_GUARDIANS      = 8;

			const MAX_GUILDMES1      = 60;
			const MAX_GUILDMES2      = 120;

			const MAX_GUILD_STORAGE  = 600;

			const DEFAULT_GUILD_MEMBERS = 16;
			const MEMBERS_PER_GUILD_EXTENSION =2;
			const MAX_GUILD             = 36; //(DEFAULT_GUILD_MEMBERS + 10 * MEMBERS_PER_GUILD_EXTENSION);
			const MAX_GUILD_POSITION    = guild::MAX_GUILD;

			const MAX_INVESTS_PER_DAY = 2;

			const MAX_DEFENSE = 100;
			const MAX_ECONOMY = 100;

			const MODE_INVITE = 0x01;
			const MODE_EXPEL = 0x10;
			const MAX_TAX = 50;

			const GD_SKILLBASE = 10000;
		}

		class homunculus
		{
			const HM_SKILLBASE = 8001;
			const MAX_HOMUNSKILL = 16;
			const MAX_HOMUNCULUS_CLASS = 16;
			const HM_CLASS_BASE = 6001;
			const HM_CLASS_MAX = 6016; // HM_CLASS_BASE+MAX_HOMUNCULUS_CLASS-1
		}
		class hotkey
		{
			const MAX_HOTKEYS = 38; // 9*4 + 2
		}

		class item
		{
			const ITEMID_HOLY_WATER      = 523;
			const ITEMID_POISONBOTTLE    = 678;
			const ITEMID_EMPERIUM        = 714;
			const ITEMID_YELLOW_GEMSTONE = 715;
			const ITEMID_RED_GEMSTONE    = 716;
			const ITEMID_BLUE_GEMSTONE   = 717;
			const ITEMID_TRAP            = 1065;
			const ITEMID_STONE           = 7049;
			const ITEMID_CRYSTAL_FRAGMENT= 7321;
			const ITEMID_SKULL_          = 7420;
			const ITEMID_WARBADGE        = 7773;
			const WEDDING_RING_M         = 2634;
			const WEDDING_RING_F         = 2635;

			const START_ITEMID           = 501;
			const START_ITEMID_CARD      = 4001;
			const MAX_ITEMID_CARD        = 4999;
			const START_ITEMID_ENCHANT   = 4700;
			const MAX_ITEMID_ENCHANT     = 4999;
			const MAX_ITEMID             = 0x7FFF;

			const CARD0_FORGE            = 0xFF;
			const MIN_ATTRIBUTE          = 0;
			const MAX_ATTRIBUTE          = 4;
			const CARD0_CREATE           = 0xFE;
			const CARD0_PET              = 0xFF00;

			const ATTRIBUTE_NORMAL       = 0;
			const MAX_STAR               = 3;



			const MAX_REFINE             = 10;
			const MAX_SLOTS              = 4;
		}

		class mail
		{
			const MAIL_MAX_INBOX = 30;
			const MAIL_TITLE_LENGTH = 40;
			const MAIL_BODY_LENGTH = 200;
		}
		class map
		{
			const MAP_NAME_LENGTH_MIN = 4;
			const MAP_NAME_LENGTH = 11;
			const MAP_NAME_LENGTH_EXT = 15; // NAME_LENGTH + strlen('.gat');
		}

		class member
		{
			const MAX_MEMBER_STORAGE = 600;
		}

		class memo
		{
			const MAX_MEMOPOINTS = 3;
		}

		class mercenary
		{
			const MC_SKILLBASE = 8201;
			const MAX_MERCSKILL = 40;
			const MAX_MERCENARY_CLASS = 44;
		}
		class party
		{
			const MAX_PARTY = 16;
		}
		class registry
		{
			const CHAR_ID = 0;
			const ACCOUNT_ID = 1;
			const MEMBER_ID = 2;

			const GLOBAL_REG  = 3;
			const ACCOUNT_REG  = 2;
			const ACCOUNT_REG2 = 1;
			const MEMBER_REG   = registry::ACCOUNT_REG2;

			const GLOBAL_REG_NUM   = 256;
			const ACCOUNT_REG_NUM  = 64;
			const ACCOUNT_REG2_NUM = 16;

		}

		class skill
		{
			const MAX_SKILL = 2356;
			const MAX_SKILL_TREE = 60;
		}


		class quest
		{
			const Q_INACTIVE = 0;
			const Q_ACTIVE   = 1;
			const Q_COMPLETE = 2;

			const MAX_QUEST_DB = 200;
			const MAX_QUEST_OBJECTIVES = 3;
		}

		// leave server @ bottom
		class server
		{
			const NAME_LENGTH = 23;
			const NAME_LENGTH_MIN = 4;
			const MAX_FAME_LIST = 10;
			const MAX_AMOUNT = 30000;
		}

    }

    namespace config\constants\ragnarok\enums
    {
        function dummy()
        {

        }
	}