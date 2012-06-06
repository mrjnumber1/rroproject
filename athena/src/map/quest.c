// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#include "../common/cbasetypes.h"
#include "../common/socket.h"
#include "../common/timer.h"
#include "../common/malloc.h"
#include "../common/nullpo.h"
#include "../common/showmsg.h"
#include "../common/strlib.h"
#include "../common/utils.h"

#include "map.h"
#include "pc.h"
#include "npc.h"
#include "itemdb.h"
#include "script.h"
#include "intif.h"
#include "battle.h"
#include "mob.h"
#include "party.h"
#include "unit.h"
#include "log.h"
#include "clif.h"
#include "quest.h"
#include "intif.h"
#include "chrif.h"
#include "pet.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdarg.h>
#include <time.h>


struct s_quest_db quest_db[MAX_QUEST_DB];
struct s_mission_db mission_db[MAX_MISSION_DB];

int quest_search_db(int quest_id)
{
	int i;

	ARR_FIND(0, MAX_QUEST_DB,i,quest_id == quest_db[i].id);
	if( i == MAX_QUEST_DB )
		return -1;

	return i;
}

//Send quest info on login
int quest_pc_login(TBL_PC * sd)
{
	nullpo_ret(sd);

	if(sd->avail_quests == 0)
		return 1;

	clif_quest_send_list(sd);
	clif_quest_send_mission(sd);

	return 0;
}

int quest_add(TBL_PC * sd, int quest_id)
{
	int i, j;

	nullpo_ret(sd);

	if( sd->num_quests >= MAX_QUEST_DB )
	{
		ShowError("quest_add: Character %d has got all the quests.(max quests: %d)\n", sd->status.char_id, MAX_QUEST_DB);
		return 1;
	}

	if( quest_check(sd, quest_id, HAVEQUEST) >= 0 )
	{
		ShowError("quest_add: Character %d already has quest %d.\n", sd->status.char_id, quest_id);
		return -1;
	}

	if( (j = quest_search_db(quest_id)) < 0 )
	{
		ShowError("quest_add: quest %d not found in DB.\n", quest_id);
		return -1;
	}

	i = sd->avail_quests;
	memmove(&sd->quest_log[i+1], &sd->quest_log[i], sizeof(struct quest)*(sd->num_quests-sd->avail_quests));
	memmove(sd->quest_index+i+1, sd->quest_index+i, sizeof(int)*(sd->num_quests-sd->avail_quests));

	memset(&sd->quest_log[i], 0, sizeof(struct quest));
	sd->quest_log[i].quest_id = quest_db[j].id;
	if( quest_db[j].time )
		sd->quest_log[i].time = (unsigned int)(time(NULL) + quest_db[j].time);
	sd->quest_log[i].state = Q_ACTIVE;

	sd->quest_index[i] = j;
	sd->num_quests++;
	sd->avail_quests++;
	sd->save_quest = true;

	clif_quest_add(sd, &sd->quest_log[i], sd->quest_index[i]);

	if( save_settings&64 )
		chrif_save(sd,0);

	return 0;
}

int quest_change(TBL_PC * sd, int qid1, int qid2)
{
	int i, j;
	nullpo_ret(sd);

	if( quest_check(sd, qid2, HAVEQUEST) >= 0 )
	{
		ShowError("quest_change: Character %d already has quest %d.\n", sd->status.char_id, qid2);
		return -1;
	}

	if( quest_check(sd, qid1, HAVEQUEST) < 0 )
	{
		ShowError("quest_change: Character %d doesn't have quest %d.\n", sd->status.char_id, qid1);
		return -1;
	}

	if( (j = quest_search_db(qid2)) < 0 )
	{
		ShowError("quest_change: quest %d not found in DB.\n",qid2);
		return -1;
	}

	ARR_FIND(0, sd->avail_quests, i, sd->quest_log[i].quest_id == qid1);
	if(i == sd->avail_quests)
	{
		ShowError("quest_change: Character %d has completed quests %d.\n", sd->status.char_id, qid1);
		return -1;
	}

	memset(&sd->quest_log[i], 0, sizeof(struct quest));
	sd->quest_log[i].quest_id = quest_db[j].id;
	if( quest_db[j].time )
		sd->quest_log[i].time = (unsigned int)(time(NULL) + quest_db[j].time);
	sd->quest_log[i].state = Q_ACTIVE;

	sd->quest_index[i] = j;
	sd->save_quest = true;

	clif_quest_delete(sd, qid1);
	clif_quest_add(sd, &sd->quest_log[i], sd->quest_index[i]);

	if( save_settings&64 )
		chrif_save(sd,0);

	return 0;
}

int quest_delete(TBL_PC * sd, int quest_id)
{
	int i;
	nullpo_ret(sd);

	//Search for quest
	ARR_FIND(0, sd->num_quests, i, sd->quest_log[i].quest_id == quest_id);
	if(i == sd->num_quests)
	{
		ShowError("quest_delete: Character %d doesn't have quest %d.\n", sd->status.char_id, quest_id);
		return -1;
	}

	if( sd->quest_log[i].state != Q_COMPLETE )
		sd->avail_quests--;
	if( sd->num_quests-- < MAX_QUEST_DB && sd->quest_log[i+1].quest_id )
	{
		memmove(&sd->quest_log[i], &sd->quest_log[i+1], sizeof(struct quest)*(sd->num_quests-i));
		memmove(sd->quest_index+i, sd->quest_index+i+1, sizeof(int)*(sd->num_quests-i));
	}
	memset(&sd->quest_log[sd->num_quests], 0, sizeof(struct quest));
	sd->quest_index[sd->num_quests] = 0;
	sd->save_quest = true;

	clif_quest_delete(sd, quest_id);

	if( save_settings&64 )
		chrif_save(sd,0);

	return 0;
}

int quest_update_objective_sub(struct block_list *bl, va_list ap)
{
	struct map_session_data * sd;
	int mob, party;

	nullpo_ret(bl);
	nullpo_ret(sd = (struct map_session_data *)bl);

	party = va_arg(ap,int);
	mob = va_arg(ap,int);

	if( !sd->avail_quests )
		return 0;
	if( sd->status.party_id != party )
		return 0;

	quest_update_objective(sd, mob);

	return 1;
}


void quest_update_objective(TBL_PC * sd, int mob)
{
	int i,j;
	nullpo_retv(sd);

	for( i = 0; i < sd->avail_quests; i++ )
	{
		if( sd->quest_log[i].state != Q_ACTIVE )
			continue;

		for( j = 0; j < MAX_QUEST_OBJECTIVES; j++ )
			if( quest_db[sd->quest_index[i]].mob[j] == mob && sd->quest_log[i].count[j] < quest_db[sd->quest_index[i]].count[j] ) 
			{
				sd->quest_log[i].count[j]++;
				sd->save_quest = true;
			//	clif_mission_info(sd, quest_db[sd->quest_index[i]].mob[j], sd->quest_log[i].count[j]);
				clif_quest_update_objective(sd,&sd->quest_log[i],sd->quest_index[i]);
			}
	}
}

int quest_update_status(TBL_PC * sd, int quest_id, quest_state status)
{
	int i;
	nullpo_ret(sd);

	//Only status of active and inactive quests can be updated. Completed quests can't (for now). [Inkfish]
	ARR_FIND(0, sd->avail_quests, i, sd->quest_log[i].quest_id == quest_id);
	if(i == sd->avail_quests)
	{
		ShowError("quest_update_status: Character %d doesn't have quest %d.\n", sd->status.char_id, quest_id);
		return -1;
	}

	sd->quest_log[i].state = status;
	sd->save_quest = true;

	if( status < Q_COMPLETE )
	{
		clif_quest_update_status(sd, quest_id, (bool)status);
		return 0;
	}

	if( i != (--sd->avail_quests) )
	{
		struct quest tmp_quest;
		memcpy(&tmp_quest, &sd->quest_log[i],sizeof(struct quest));
		memcpy(&sd->quest_log[i], &sd->quest_log[sd->avail_quests],sizeof(struct quest));
		memcpy(&sd->quest_log[sd->avail_quests], &tmp_quest,sizeof(struct quest));
	}

	clif_quest_delete(sd, quest_id);

	if( save_settings&64 )
		chrif_save(sd,0);

	return 0;
}

int quest_check(TBL_PC * sd, int quest_id, quest_check_type type)
{
	int i;
	nullpo_ret(sd);

	ARR_FIND(0, sd->num_quests, i, sd->quest_log[i].quest_id == quest_id);
	if(i == sd->num_quests)
		return -1;

	switch( type )
	{
	case HAVEQUEST:		
		return sd->quest_log[i].state;
	case PLAYTIME:
		return (sd->quest_log[i].time < (unsigned int)time(NULL) ? 2 : sd->quest_log[i].state == Q_COMPLETE ? 1 : 0);
	case HUNTING:
		{
			if( sd->quest_log[i].state == 0 || sd->quest_log[i].state == 1 ) 
			{
				int j;
				ARR_FIND(0, MAX_QUEST_OBJECTIVES, j, sd->quest_log[i].count[j] < quest_db[sd->quest_index[i]].count[j]);
				if( j == MAX_QUEST_OBJECTIVES )
					return 2;
				if( sd->quest_log[i].time < (unsigned int)time(NULL) )
					return 1;
				return 0;
			}
			else
				return 0;
		}
	default:
		ShowError("quest_check_quest: Unknown parameter %d",type);
		break;
	}

	return -1;
}

void mission_announce_loadevent(struct map_session_data *sd, int mapindex)
{
	int i, j, last_kill;
	char reg[NAME_LENGTH],out[CHAT_SIZE_MAX];

	ARR_FIND(0, MAX_MISSION_DB, i, mapindex == mission_db[i].mapindex);

	if (i==MAX_MISSION_DB)
		return;
	// i is now the relevant mission id!

	// if the player is out of range
	if (sd->status.base_level > mission_db[i].max_level || sd->status.base_level < mission_db[i].min_level)
		return;

	// check all mission slots for the player
	for (j=0; j < MAX_MISSION_SLOTS; ++j)
	{
		//if the slot is not free (and if the player already has the mission, as well!)
		if (sd->mission[j].mission_id > 0) 
			return;
	}

	// if the player recently completed the mission
	snprintf(reg, sizeof(reg), "MD_%d_time", mission_db[i].id);
	last_kill = pc_readglobalreg(sd, reg);
	if ( ((int)time(NULL) - last_kill) < mission_db[i].cooldown) 
		return;

	//yay finally announce if possible!
	snprintf(out, sizeof(out), "The mission [%s] is recommended for this map.", mission_db[i].name);
	clif_coloredMessage(&sd->bl, out, 0x00FFFF, SELF);
}

void mission_remove(struct map_session_data *sd, int index)
{
	int i;
	char reg[NAME_LENGTH];

	nullpo_retv(sd);

	if (index > MAX_MISSION_SLOTS || index < 0)
	{
		ShowError("mission_remove: Invalid index: %d (c:%d)\n", index, sd->status.char_id);
		return;
	}

	// at this point, sd->mission[index] is unneeded/trash so we clear it
	memset(&sd->mission[index], 0, sizeof (sd->mission[index]));

	// reset character registries to 0 on removal
	snprintf(reg, NAME_LENGTH, "MD_slot%d_ID", index);
	pc_setglobalreg(sd, reg, 0);

	for (i=0; i<MISSION_MAX_MOBS; ++i)
	{
		snprintf(reg, NAME_LENGTH, "MD_slot%d_mCount%d", index, i);
		pc_setglobalreg(sd, reg, 0);

	}

	return;
}

int mission_check_status(struct map_session_data *sd, int i)
{
	nullpo_ret(sd);

	if (i > MAX_MISSION_SLOTS || i < 0) 
	{
		ShowError("mission_check_status: Invalid index: %d (C:%d)\n", i, sd->status.char_id);
		return MISSION_NOT_FOUND;
	}


	if (   mission_check_gathering_status(sd, i) == MISSION_GATHERING_COMPLETE 
		&& mission_check_hunting_status(sd, i)   == MISSION_HUNTING_COMPLETE
	   )
		return MISSION_COMPLETE;

	
	return MISSION_INCOMPLETE;
}
int mission_check_gathering_status(struct map_session_data *sd, int i)
{
	int j;

	nullpo_ret(sd);


	if (i > MAX_MISSION_SLOTS || i < 0) 
	{
		ShowError("mission_check_gathering_status: Invalid index: %d (C:%d)\n", i, sd->status.char_id);
		return MISSION_NOT_FOUND;
	}
	else
	{
		if(sd->mission[i].mission_id == 0)
			return MISSION_AVAILABLE_SLOT;

		for (j=0; j < MISSION_MAX_ITEMS; ++j)
		{
			int n;
			if(!sd->mission[i].item[j].id)
				continue;

			n = pc_search_inventory(sd, sd->mission[i].item[j].id);
			// remember; 0 is a valid inventory index!
			if( n < 0 || (sd->status.inventory[n].amount < sd->mission[i].item[j].goal) )
				return MISSION_GATHERING_INCOMPLETE;

		}

		return MISSION_GATHERING_COMPLETE;
	}

	ShowError("mission_check_gathering_status: Invalid control reached: %d (C:%d)\n", i, sd->status.char_id); 
	return MISSION_NOT_FOUND;
}

int mission_check_hunting_status(struct map_session_data *sd, int i)
{
	int j;

	nullpo_ret(sd);


	if (i > MAX_MISSION_SLOTS || i < 0) 
	{
		ShowError("mission_check_hunting_status: Invalid index: %d (C:%d)\n", i, sd->status.char_id);
		return MISSION_NOT_FOUND;
	}
	else
	{
		if(sd->mission[i].mission_id == 0)
			return MISSION_AVAILABLE_SLOT;

		for (j=0; j < MISSION_MAX_MOBS; ++j)
			if(sd->mission[i].mob[j].id && sd->mission[i].mob[j].killed < sd->mission[i].mob[j].goal)
				return MISSION_HUNTING_INCOMPLETE;

		return MISSION_HUNTING_COMPLETE;
	}

	ShowError("mission_check_hunting_status: Invalid control reached: %d (C:%d)", i, sd->status.char_id); 
	return MISSION_NOT_FOUND;
}

int mission_assign(struct map_session_data *sd, int id, int index)
{
	int i, j;
	char reg[NAME_LENGTH];

	nullpo_ret(sd);

	if(sd->mission[index].mission_id < 1)
	{
		ARR_FIND(0,MAX_MISSION_DB,i,mission_db[i].id==id);

		if (i == MAX_MISSION_DB)
			return MISSION_NOT_FOUND;

		memset(&sd->mission[index], 0, sizeof(sd->mission[index]));
		sd->mission[index].mission_id = id;
		snprintf(reg, NAME_LENGTH, "MD_slot%d_ID", index);
		pc_setglobalreg(sd, reg, id);
		
		for (j=0; j < MISSION_MAX_MOBS; ++j)
		{
			if (!mission_db[i].mob[j].id)
				continue;

			sd->mission[index].mob[j].id = mission_db[i].mob[j].id;
			sd->mission[index].mob[j].goal = mission_db[i].mob[j].count;
			snprintf(reg, NAME_LENGTH, "MD_slot%d_mCount%d", index, j);
			pc_setglobalreg(sd, reg, 0);
			sd->mission[index].mob[j].killed = 0;
		}

		for (j=0; j < MISSION_MAX_ITEMS; ++j)
		{
			if (!mission_db[i].mob[j].id)
				continue;

			sd->mission[index].item[j].id = mission_db[i].item[j].id;
			sd->mission[index].item[j].goal = mission_db[i].item[j].count;
		}
		
		return MISSION_INCOMPLETE;
	}
	else
		return MISSION_UNAVAILABLE_SLOT;


}

int mission_update(struct map_session_data *sd, int mob_id)
{
	int i, j;
	struct mob_db *mob;
	char out[CHAT_SIZE_MAX];
	char reg[NAME_LENGTH];

	nullpo_ret(sd);

	if(!mob_id)
		return 0;

	if (mob_db(mob_id) == NULL)
	{
		ShowError("mission_update: Bad Mob ID (%d) passed for Char (%d)\n", mob_id, sd->status.char_id); 
		return 0;
	}

	for (i=0; i < MAX_MISSION_SLOTS; ++i)
	{
		if (sd->mission[i].mission_id < 1)
			continue;

		for (j=0; j < MISSION_MAX_MOBS; ++j)
		{
			if (mob_id == sd->mission[i].mob[j].id && (mob = mob_db(sd->mission[i].mob[j].id)) != NULL && sd->mission[i].mob[j].killed < sd->mission[i].mob[j].goal)
			{	
				++sd->mission[i].mob[j].killed;

				snprintf(reg, NAME_LENGTH, "MD_slot%d_mCount%d", i, j);
				pc_setglobalreg(sd, reg, sd->mission[i].mob[j].killed);

				if (sd->mission[i].mob[j].killed < sd->mission[i].mob[j].goal)
					snprintf(out, CHAT_SIZE_MAX, "[%s] %s: (%d/%d)", 
						mission_db[sd->mission[i].mission_id-1].name, mob->jname, sd->mission[i].mob[j].killed, sd->mission[i].mob[j].goal);
				else
				{

					if (mission_check_hunting_status(sd, i) == MISSION_HUNTING_COMPLETE)
						snprintf(out, CHAT_SIZE_MAX, "[%s] finished hunting all mobs!", mission_db[sd->mission[i].mission_id-1].name);
					else
						snprintf(out, CHAT_SIZE_MAX, "[%s] finished hunting `%s`!", mission_db[sd->mission[i].mission_id -1].name, mob->jname);	
				}
				clif_coloredMessage(&sd->bl, out, 0xFF001F, SELF);
					
				return 1;
			}
		}
	}

	return 0;
}

int mission_update_sub(struct block_list *bl, va_list ap)
{
	//map_foreachinrange(quest_update_mission_sub,&md->bl, AREA_SIZE, BL_PC, sd->status.party_id, md->class_);
	struct map_session_data * sd;
	struct party_data *p;
	int mob_id, party;

	nullpo_ret(bl);
	nullpo_ret(sd = (struct map_session_data *)bl);

	party = va_arg(ap,int);
	mob_id = va_arg(ap,int);

	if (sd->status.party_id != party)
		return 0;

	if( (p=party_search(party)) == NULL)
		return 0;
	
	if( !party_check_exp_share(p) ) // if the party cannot share exp, then no one but the killer will get a mission kill
		return 0;

	return mission_update(sd, mob_id);
}

bool mission_submit(struct map_session_data *sd, int index)
{
	unsigned int base=0, job=0;
	int nameid[MISSION_MAX_REWARDS], count[MISSION_MAX_REWARDS];
	int i;
	char reg[NAME_LENGTH], msg[CHAT_SIZE_MAX];
	bool check_add = false;

	nullpo_ret(sd);

	if(sd->mission[index].mission_id < 1 || sd->mission[index].mission_id-1 > MAX_MISSION_DB)
	{
		ShowError("mission_submit:Invalid Mission ID for c:%d\n", sd->status.char_id);
		return false;
	}

	if(mission_check_status(sd, index) != MISSION_COMPLETE)
		return false;

	for (i=0; i < MISSION_MAX_REWARDS; ++i)
	{
		int id = mission_db[sd->mission[index].mission_id-1].reward[i].id;
		int amount =  mission_db[sd->mission[index].mission_id-1].reward[i].count;

		if (!id)
			continue;

		nameid[i] = id?id:0;
		count[i] = amount?amount:0;

		if (!check_add && id)
			check_add = true;
	}

	if (check_add && !pc_can_add_items(sd, nameid, count) )
		return false;

	// all pre-reqs checked! proceed to give rewards

	for (i=0; i < MISSION_MAX_ITEMS; ++i)
	{
		int id = mission_db[sd->mission[index].mission_id-1].item[i].id; 
		int amount = mission_db[sd->mission[index].mission_id-1].item[i].count;
		int n;
		struct item* inv;

		if (!id)
			continue;

		n = pc_search_inventory(sd, id);

		
		if(n < 0)
			return false; //this should be impossible to reach! 


		// for later code, we assume the player has the items, already checked in mission_check_status
		// who wants to bet on how likely this is to lead to an exploit? 
		
		inv = &sd->status.inventory[n];

		log_pick_pc(sd, LOG_TYPE_SCRIPT, inv->nameid, -amount, inv, inv->serial);
		//Logs
		pc_delitem(sd, n, amount, 0, 0);
	}

	base = mission_db[sd->mission[index].mission_id-1].base_exp;
	job = mission_db[sd->mission[index].mission_id-1].job_exp;

	pc_gainexp(sd, NULL, base, job, true);

	if (check_add)
	{
		for (i=0; i < MISSION_MAX_REWARDS; ++i)
		{
			struct item it;
			int get_count,j,flag=0;

			memset(&it, 0, sizeof(struct item));

			if(nameid[i] == 0)
				continue;

			it.nameid = nameid[i];
			it.identify = 1;

			if (!itemdb_isstackable(nameid[i]))
				get_count = 1;
			else
				get_count = count[i];

			for (j=0; j < count[i]; ++j)
			{
				if ( !pet_create_egg(sd, nameid[i]) )
				{
					if ( (flag=pc_additem(sd, &it, get_count)) )
					{
						clif_additem(sd, 0, 0, flag);
						if ( pc_candrop(sd, &it) )
							map_addflooritem(&it, get_count, sd->bl.m, sd->bl.x, sd->bl.y, 0, 0, 0, 0);
					}
				}

				log_pick_pc(sd, LOG_TYPE_SCRIPT, nameid[i], get_count, NULL, it.serial);
				it.serial = 0;
			}

		}
	}

	snprintf(reg, sizeof(reg), "MD_%d_time", sd->mission[index].mission_id);
	pc_setglobalreg(sd, reg, (int)time(NULL));
	pc_setglobalreg(sd, "MD_missions_completed", pc_readglobalreg(sd, "MD_missions_completed") + 1);
	pc_setglobalreg(sd, "MD_mission_points", pc_readglobalreg(sd, "MD_mission_points") + mission_db[sd->mission[index].mission_id-1].points);
	
	snprintf(msg, sizeof(msg), "You gained %d Mission Points!", mission_db[sd->mission[index].mission_id-1].points);
	clif_displaymessage(sd->fd, msg);

	mission_remove(sd, index);

	return true;
}

static void mission_set_exp(int k)
{
	int i;
	struct mob_db *mob = NULL;
	unsigned int mob_bexp=0, mob_jexp=0;
	double bexp=0, jexp=0;
	int bonus;
 
	for (i=0; i < MISSION_MAX_MOBS; ++i)
	{
		if(mission_db[k].mob[i].id && (mob = mob_db(mission_db[k].mob[i].id)) != NULL) // it should never find a null mob id!
		{
			mob_bexp = mob->base_exp / (battle_config.base_exp_rate/100);
			mob_jexp = mob->job_exp  / (battle_config.job_exp_rate/100);

			if(mob->status.mode&MD_BOSS)
				bonus = battle_config.mission_boss_mob_bonus_exp;
			else
				bonus = battle_config.mission_normal_mob_bonus_exp;

			if(mob_bexp)
				bexp = bexp + (double)((mob_bexp * bonus / 100) * mission_db[k].mob[i].count);
			if(mob_jexp)
				jexp = jexp + (double)((mob_jexp * bonus / 100) * mission_db[k].mob[i].count);

		}
	}

	//ShowInfo("missionID: %d | bexp: %u | jexp: %u\n", mission_db[k].id, (unsigned int)bexp, (unsigned int)jexp);
	bexp = bexp * ((double)mission_db[k].exp_multiplier/100. * (double)battle_config.mission_bonus_exp/100. * (double)battle_config.base_exp_rate/100.);
	jexp = jexp * ((double)mission_db[k].exp_multiplier/100. * (double)battle_config.mission_bonus_exp/100. * (double)battle_config.job_exp_rate /100.);

	mission_db[k].base_exp = (unsigned int)cap_value(bexp, 0, (UINT_MAX/8)); // no quest should give over 500million EXP!
	mission_db[k].job_exp  = (unsigned int)cap_value(jexp, 0, (UINT_MAX/8));
}

static bool mission_parse_mission_db(char* str[], int columns, int k)
{
	int i;

	mission_db[k].id = atoi(str[0]);

	mission_db[k].mapindex = map_mapname2mapid(str[1]);
	
	map[mission_db[k].mapindex].flag.loadevent = 1;

	mission_db[k].min_level = atoi(str[2]);
	mission_db[k].max_level = atoi(str[3]);

	mission_db[k].cooldown = atoi(str[4]);

	mission_db[k].exp_multiplier = atoi(str[5]);


	for (i=0; i < MISSION_MAX_MOBS; i++)
	{
		mission_db[k].mob[i].id = atoi(str[2*i+6]);

		if (!mission_db[k].mob[i].id)
			break;

		if ( !mob_db(mission_db[k].mob[i].id) )
		{
			ShowError("mission_parse_mission_db: Mob ID '%d' passed for quest '%d' does not exist! It will be removed.\n", mission_db[k].mob[i].id, k);
			mission_db[k].mob[i].id = 0;
			continue;
		}
		mission_db[k].mob[i].count = atoi(str[2*i+7]);
			
		if (mission_db[k].mob[i].id && !mission_db[k].mob[i].count)
		{
			ShowError("mission_parse_mission_db: Mob ID '%d' passed for quest '%d', but no count given! Mob ID will be removed.\n", mission_db[k].mob[i].id, k);
			mission_db[k].mob[i].id = 0;
			continue;
		}
	}

	for (i=0; i < MISSION_MAX_ITEMS; i++)
	{
		mission_db[k].item[i].id = atoi(str[2*i+1+MISSION_MAX_MOBS*2+6]);

		if (!mission_db[k].item[i].id)
			break;

		if (!itemdb_exists(mission_db[k].item[i].id))
		{
			ShowError("mission_parse_mission_db: Item ID '%d' passed for quest '%d', but it is an invalid item! It will be removed.\n", mission_db[k].item[i].id, k);
			mission_db[k].item[i].id = 0;
			continue;
		}

		mission_db[k].item[i].count = atoi(str[2*i+2+MISSION_MAX_MOBS*2+6]);
			
		if ( mission_db[k].item[i].id && !mission_db[k].item[i].count )
		{
			ShowError("mission_parse_mission_db: Item ID '%d' passed for quest '%d', but no count was given! Item ID will be removed.\n", mission_db[k].item[i].id, k);
			mission_db[k].item[i].id = 0;
			continue;
		}
	}
		
	for (i=0; i < MISSION_MAX_REWARDS; i++)
	{
		mission_db[k].reward[i].id = atoi(str[2*i+1+MISSION_MAX_MOBS*2+MISSION_MAX_ITEMS*2+6]);

		if (!mission_db[k].reward[i].id)
			break;

		if (!itemdb_exists(mission_db[k].reward[i].id))
		{
			ShowError("mission_parse_mission_db: Item ID '%d' passed for quest '%d', but it is an invalid item! It will be removed.\n", mission_db[k].reward[i].id, k);
			mission_db[k].reward[i].id = 0;
			continue;
		}


		mission_db[k].reward[i].count = atoi(str[2*i+2+MISSION_MAX_MOBS*2+MISSION_MAX_ITEMS*2+6]);
			
		if ( mission_db[k].reward[i].id && !mission_db[k].reward[i].count )
		{
			ShowError("mission_parse_mission_db: Reward ID '%d' passed for quest '%d', but no count given! Reward ID will be removed.\n", mission_db[k].reward[i].id, k);
			mission_db[k].reward[i].id = 0;
			continue;
		}
	}

	i = 6+2*(MISSION_MAX_MOBS+MISSION_MAX_ITEMS+MISSION_MAX_REWARDS);

	mission_db[k].points = atoi(str[i]);

	safestrncpy(mission_db[k].name, str[i+1], sizeof(mission_db[k].name));
		
	mission_set_exp(k);

	return true;
}

static void mission_read_db()
{
#define MISSION_DB_COLUMNS (8+2*(MISSION_MAX_MOBS+MISSION_MAX_ITEMS+MISSION_MAX_REWARDS))

	sv_readdb(db_path, "mission_db.txt", ',', MISSION_DB_COLUMNS, MISSION_DB_COLUMNS, MAX_MISSION_DB, mission_parse_mission_db); 

#undef MISSION_DB_COLUMNS
}

void mission_reload()
{
	mission_read_db();
}


int quest_read_db(void)
{
	FILE *fp;
	char line[1024];
	int i,j,k = 0;
	char *str[20],*p,*np;

	sprintf(line, "%s/quest_db.txt", db_path);
	if( (fp=fopen(line,"r"))==NULL ){
		ShowError("can't read %s\n", line);
		return -1;
	}
	
	while(fgets(line, sizeof(line), fp))
	{
		if (k == MAX_QUEST_DB) {
			ShowError("quest_read_db: Too many entries specified in %s/quest_db.txt!\n", db_path);
			break;
		}
		if(line[0]=='/' && line[1]=='/')
			continue;
		memset(str,0,sizeof(str));

		for( j = 0, p = line; j < 8;j++ )
		{
			if((np=strchr(p,','))!=NULL)
			{
				str[j] = p;
				*np = 0;
				p = np + 1;
			}
			else if (str[0] == NULL)
				continue;
			else
			{
				ShowError("quest_read_db: insufficient columns in line %s\n", line);
				continue;
			}
		}
		if(str[0]==NULL)
			continue;

		memset(&quest_db[k], 0, sizeof(quest_db[0]));

		quest_db[k].id = atoi(str[0]);
		quest_db[k].time = atoi(str[1]);
		for( i = 0; i < MAX_QUEST_OBJECTIVES; i++ )
		{
			quest_db[k].mob[i] = atoi(str[2*i+2]);
			quest_db[k].count[i] = atoi(str[2*i+3]);

			if( !quest_db[k].mob[i] || !quest_db[k].count[i] )
				break;
		}
		quest_db[k].num_objectives = i;
		//memcpy(quest_db[k].name, str[8], sizeof(str[8]));
		k++;
	}
	fclose(fp);
	ShowStatus("Done reading '"CL_WHITE"%d"CL_RESET"' entries in '"CL_WHITE"%s"CL_RESET"'.\n", k, "quest_db.txt");
	return 0;
}


void do_final_quest(void)
{

}
void do_init_quest(void)
{
	quest_read_db();
	mission_read_db();
}
