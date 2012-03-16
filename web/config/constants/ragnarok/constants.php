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

		class auction
		{
			const MIN_HOURS = 1;
			const MAX_HOURS = 48;
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
			const MAX_DEFENSE = 99;

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
			const MEMBERS_PER_GUILD_EXTENSION = 3;
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

			const MAX_INTIMACY = 100000;
			const MAX_HUNGER = 100;
		}
		class hotkey
		{
			const MAX_HOTKEYS = 38; // 9*4 + 2
			const TYPE_SKILL = 1;
			const TYPE_ITEM = 0;
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

			const MAIL_NEW = 0;
			const MAIL_UNREAD = 1;
			const MAIL_READ = 2;
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
			// the difference between MERC_CLASS_MAX and MAX_MERCENARY_CLASS is that
			// MERC_CLASS_MAX refers only to sword/arch/spear, not the monster types ex mimic/alice/etc
			const MAX_MERCENARY_CLASS = 44;

			const MERC_CLASS_BASE = 6017;
			const MERC_ARCH_BASE = 6017;
			const MERC_SPEAR_BASE = 6027;
			const MERC_SWORD_BASE = 6037;
			const MERC_CLASS_MAX = 6046;

			const ARCH_MERC_GUILD = 0;
			const SPEAR_MERC_GUILD = 1;
			const SWORD_MERC_GUILD = 2;
		}

		class mob_db
		{
			const MOB_DB_START = 1001;
			const MAX_MOB_DB = 4000;
			const MAX_MOB_DROP = 10;

			const MOB_CLONE_START = 3001;
			const MOB_CLONE_END = 4000;

			const MOBID_EMPERIUM = 1288;
			const MOBID_BARRICADE1 = 1905;
			const MOBID_BARRICADE2 = 1906;
			const MOBID_GUARDIAN_STONE1 = 1907;
			const MOBID_GUARDIAN_STONE2 = 1908;

			const MOBID_BG_BARRICADE = 1906;
			const MOBID_BG_GUARDIAN_STONE1 = 2105;
			const MOBID_BG_GUARDIAN_STONE2 = 2106;
			const MOBID_BG_EMPERIUM = 2107;

			const MD_CANMOVE            = 0x0001;
			const MD_LOOTER             = 0x0002;
			const MD_AGGRESSIVE         = 0x0004;
			const MD_ASSIST             = 0x0008;
			const MD_CASTSENSOR_IDLE    = 0x0010;
			const MD_BOSS               = 0x0020;
			const MD_PLANT              = 0x0040;
			const MD_CANATTACK          = 0x0080;
			const MD_DETECTOR           = 0x0100;
			const MD_CASTSENSOR_CHASE   = 0x0200;
			const MD_CHANGECHASE        = 0x0400;
			const MD_ANGRY              = 0x0800;
			const MD_CHANGETARGET_MELEE = 0x1000;
			const MD_CHANGETARGET_CHASE = 0x2000;
			const MD_TARGETWEAK         = 0x4000;
			const MD_MASK               = 0xFFFF;
		}

		class party
		{
			const MAX_PARTY = 16;

			const OPT_PICKUPSHARE = 1;
			const OPT_REDISTRIBUTE = 2;
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

		class status_change
		{
			const SC_MAX = 332;
		}

		class quest
		{
			const Q_INACTIVE = 0;
			const Q_ACTIVE   = 1;
			const Q_COMPLETE = 2;

			const MAX_QUEST_DB = 200;
			const MAX_QUEST_OBJECTIVES = 3;
		}

		// leave server @ bottom please :)
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