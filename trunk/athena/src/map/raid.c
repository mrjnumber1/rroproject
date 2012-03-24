// Copyright (c) mrjs butt - Licensed under GNU GPL
// For more information, see LICENCE in the main folder
#if 0

#include "../common/cbasetypes.h"
#include "../common/timer.h"
#include "../common/malloc.h"
#include "../common/nullpo.h"
#include "../common/showmsg.h"
#include "../common/socket.h"
#include "../common/strlib.h"
#include "../common/utils.h"

#include "raid.h"
#include "battleground.h"
#include "battle.h"
#include "clif.h"
#include "itemdb.h"
#include "map.h"
#include "npc.h"
#include "pc.h"
#include "pet.h"
#include "homunculus.h"
#include "mercenary.h"
#include "party.h"
#include "log.h"
#include "quest.h"
#include "skill.h"

#include <string.h>
#include <stdio.h>


static DBMap* raid_team_db; // int bg_id -> struct battleground_data*
static unsigned int raid_team_counter = 0; // Next bg_id
struct guild raid_guild[20]; // Temporary fake guild information


int raid_checkskill(struct raid_data *rd, int id)
{
	int idx = id - GD_SKILLBASE;
	if( idx < 0 || idx >= MAX_GUILDSKILL || !rd->g )
		return 0;
	return rd->g->skill[idx].lv;
}


int raid_block_skill_end(int tid, unsigned int tick, int id, intptr data)
{
	struct raid_data *rd;
	char output[128];
	int idx = battle_config.guild_skills_separate_delay ? (int)data - GD_SKILLBASE : 0;

	if( (rd = raid_team_search(id)) == NULL )
		return 1;

	if( idx < 0 || idx >= MAX_GUILDSKILL )
	{
		ShowError("raid_block_skill_end invalid skillnum %d.\n", (int)data);
		return 0;
	}

	if( tid != rd->skill_block_timer[idx] )
	{
		ShowError("raid_block_skill_end %d != %d.\n", rd->skill_block_timer[idx], tid);
		return 0;
	}

	sprintf(output, "%s: Guild Skill %s Ready!!", rd->g->name, skill_get_desc((int)data));
	clif_raid_message(rd, rd->raid_id, rd->g->name, output, strlen(output) + 1);
	rd->skill_block_timer[idx] = INVALID_TIMER;

	return 1;
}
void raid_block_skill_status(struct raid_data *rd, int skillnum)
{
	const struct TimerData * td;
	char output[128];
	int seconds, idx;

	idx = battle_config.guild_skills_separate_delay ? skillnum - GD_SKILLBASE : 0;
	if( rd == NULL || rd->g == NULL || idx < 0 || idx >= MAX_GUILDSKILL || rd->skill_block_timer[idx] == INVALID_TIMER )
		return;

	if( (td = get_timer(rd->skill_block_timer[idx])) == NULL )
		return;

	seconds = DIFF_TICK(td->tick,gettick())/1000;
	sprintf(output, "%s : Cannot use team skill %s. %d seconds remaining...", rd->g->name, skill_get_desc(skillnum), seconds);
	clif_raid_message(rd, rd->raid_id, rd->g->name, output, strlen(output) + 1);
}

void raid_block_skill_start(struct raid_data *rd, int skillnum, int time)
{
	int idx = battle_config.guild_skills_separate_delay ? skillnum - GD_SKILLBASE : 0;
	if( rd == NULL || idx < 0 || idx >= MAX_GUILDSKILL )
		return;

	if( rd->skill_block_timer[idx] != INVALID_TIMER )
		delete_timer(rd->skill_block_timer[idx], raid_block_skill_end);

	rd->skill_block_timer[idx] = add_timer(gettick() + time, raid_block_skill_end, rd->raid_id, skillnum);
}

struct raid_data* raid_team_search(int rd_id)
{ // Search a BG Team using bg_id
	if( !rd_id ) return NULL;
	return (struct raid_data *)idb_get(raid_team_db, rd_id);
}

struct map_session_data* raid_getavailablesd(struct raid_data *rd)
{
	int i;
	nullpo_retr(NULL, rd);

	ARR_FIND(0, MAX_RAID_MEMBERS, i, rd->members[i].sd != NULL);
	return( i < MAX_RAID_MEMBERS ) ? rd->members[i].sd : NULL;
}

int raid_team_clean(int raid_id, bool remove)
{ // Deletes raid Team from db
	int i;
	struct map_session_data *sd;
	struct raid_data *rd = raid_team_search(raid_id);
	struct guild *g;

	if( rd == NULL ) return 0;
	for( i = 0; i < MAX_RAID_MEMBERS; i++ )
	{
		if( (sd = rd->members[i].sd) == NULL )
			continue;


		raid_send_dot_remove(sd);
		sd->state.raid_id = 0;
		sd->state.rmaster_flag = NULL;


		if( sd->status.guild_id && (g = guild_search(sd->status.guild_id)) != NULL )
		{
			clif_guild_belonginfo(sd,g);
			clif_guild_basicinfo(sd);
			clif_guild_allianceinfo(sd);
			clif_guild_memberlist(sd);
			clif_guild_skillinfo(sd);
		}
		else
			clif_raid_leave_single(sd, sd->status.name, "Leaving raid...");

		clif_charnameupdate(sd);
		clif_guild_emblem_area(&sd->bl);
	}

	for( i = 0; i < MAX_GUILDSKILL; i++ )
	{
		if( rd->skill_block_timer[i] == INVALID_TIMER )
			continue;
		delete_timer(rd->skill_block_timer[i], raid_block_skill_end);
	}

	if(remove)
		idb_remove(raid_team_db, raid_id);
	else
	{
		rd->count =0;
		rd->leader_char_id =0;
		rd->team_score = 0;
		rd->creation_tick = 0;
		memset(&rd->members, 0, sizeof(rd->members));
	}

	return 1;
}

int raid_team_warp(int raid_id, unsigned short mapindex, short x, short y)
{ // Warps a Team
	int i;
	struct raid_data *rd = raid_team_search(raid_id);
	if( rd == NULL ) 
		return 0;

	if( mapindex == 0 )
	{
		mapindex = rd->mapindex;
		x = rd->x;
		y = rd->y;
	}

	for( i = 0; i < MAX_RAID_MEMBERS; i++ )
		if( rd->members[i].sd != NULL ) 
			pc_setpos(rd->members[i].sd, mapindex, x, y, CLR_TELEPORT);
	return 1;
}

int raid_team_get_id(struct block_list *bl)
{
	if( bl )
	{
		switch( bl->type )
		{
			case BL_PC:
				return ((TBL_PC*)bl)->state.raid_id;
			case BL_PET:
				if( ((TBL_PET*)bl)->msd )
					return ((TBL_PET*)bl)->msd->state.raid_id;
				break;
			case BL_MOB:
			{
				struct map_session_data *msd;
				struct mob_data *md = (TBL_MOB*)bl;
				if( md->special_state.ai && (msd = map_id2sd(md->master_id)) != NULL )
					return msd->state.raid_id;
				return md->state.raid_id;
			}
			case BL_HOM:
				if( ((TBL_HOM*)bl)->master )
					return ((TBL_HOM*)bl)->master->state.raid_id;
				break;
			case BL_MER:
				if( ((TBL_MER*)bl)->master )
					return ((TBL_MER*)bl)->master->state.raid_id;
				break;
			case BL_SKILL:
				return ((TBL_SKILL*)bl)->group->raid_id;
			case BL_NPC:
				return ((TBL_NPC*)bl)->u.scr.raid_id;
		}
	}

	return 0;
}

int raid_send_message(struct map_session_data *sd, const char *mes, int len)
{
	struct raid_data *rd;

	nullpo_ret(sd);
	if( sd->state.raid_id == 0 || (rd = raid_team_search(sd->state.raid_id)) == NULL )
		return 0;
	clif_raid_message(rd, sd->bl.id, sd->status.name, mes, len);
	return 0;
}

int raid_reveal_pos(struct block_list *bl, va_list ap)
{
	struct map_session_data *pl_sd, *sd = NULL;
	int flag, color;

	pl_sd = (struct map_session_data *)bl;
	sd = va_arg(ap,struct map_session_data *); // Source
	flag = va_arg(ap,int);
	color = va_arg(ap,int);

	if( pl_sd->state.raid_id == sd->state.raid_id )
		return 0; // Same Team

	clif_viewpoint(pl_sd,sd->bl.id,flag,sd->bl.x,sd->bl.y,sd->bl.id,color);
	return 0;
}

int raid_send_xy_timer_sub(DBKey key, void *data, va_list ap)
{
	struct raid_data *rd = (struct raid_data *)data;
	struct map_session_data *sd;
	char output[128];
	int i, m;
	
	nullpo_ret(rd);

	m = map_mapindex2mapid(rd->mapindex);
	rd->reveal_flag = !rd->reveal_flag; // Switch

	for( i = 0; i < MAX_BG_MEMBERS; i++ )
	{
		if( (sd = rd->members[i].sd) == NULL )
			continue;

		if( sd->bl.x != rd->members[i].x || sd->bl.y != rd->members[i].y )
		{ // xy update
			rd->members[i].x = sd->bl.x;
			rd->members[i].y = sd->bl.y;
			clif_raid_xy(sd);
		}
		if( rd->reveal_pos && rd->reveal_flag && sd->bl.m == m ) // Reveal each 4 seconds
			map_foreachinmap(raid_reveal_pos,m,BL_PC,sd,1,rd->color);



/*		if( battle_config.bg_idle_announce && !sd->state.bg_afk && DIFF_TICK(last_tick, sd->idletime) >= battle_config.bg_idle_announce && bg->g )
		{ // Idle announces
			char output[128];
			sd->state.bg_afk = 1;
			sprintf(output, "%s : %s seems to be away! A Can be kicked out by Team Leader with @reportafk", bg->g->name, sd->status.name);

			clif_bg_message(bg, bg->bg_id, bg->g->name, output, strlen(output) + 1);
		}
		/**/
	}

	return 0;
}

int raid_send_xy_timer(int tid, unsigned int tick, int id, intptr data)
{
	raid_team_db->foreach(raid_team_db, raid_send_xy_timer_sub, tick);
	return 0;
}

int raid_team_join(int raid_id, struct map_session_data *sd)
{ // Player joins team
	int i;
	struct raid_data *rd = raid_team_search(raid_id);
	struct map_session_data *pl_sd;

	if( rd == NULL || sd == NULL || sd->state.raid_id ) return 0;

	ARR_FIND(0, MAX_RAID_MEMBERS, i, rd->members[i].sd == NULL);
	if( i == MAX_RAID_MEMBERS ) return 0; // No free slots

	pc_update_last_action(sd,0); // Start count from here...
	sd->state.raid_id = raid_id;

	//memset(&sd->status.raid_round_stats, 0, sizeof(sd->status.raid_round_stats));

	//sd->state.raid_afk = 0;
	rd->members[i].sd = sd;
	rd->members[i].x = sd->bl.x;
	rd->members[i].y = sd->bl.y;
	rd->count++;



	if( rd->leader_char_id == 0 )
	{ // First Join = Team Leader
		rd->leader_char_id = sd->status.char_id;
		sd->state.rmaster_flag = rd;
	}

	guild_send_dot_remove(sd);
	clif_raid_belonginfo(sd);
	// clif_raid_emblem(sd, rd->g);
	clif_charnameupdate(sd);

	for( i = 0; i < MAX_RAID_MEMBERS; i++ )
	{
		if( (pl_sd = rd->members[i].sd) == NULL )
			continue;
		clif_guild_basicinfo(pl_sd);
		clif_raid_emblem(pl_sd, rd->g);
		clif_raid_memberlist(pl_sd);
		if( pl_sd != sd )
			clif_hpmeter_single(sd->fd, pl_sd->bl.id, pl_sd->battle_status.hp, pl_sd->battle_status.max_hp);
	}

	clif_guild_emblem_area(&sd->bl);
	clif_raid_hp(sd);
	clif_raid_xy(sd);

	return 1;
}

int raid_team_leave(struct map_session_data *sd, int flag)
{ // Single Player leaves team
	int i, raid_id;
	struct raid_data *rd;
	struct map_session_data *pl_sd;
	char output[128];
	struct guild *g;

	if( sd == NULL || !sd->state.raid_id )
		return 0;

	// Packets
	clif_raid_leave_single(sd, sd->status.name, "Leaving raid.");
	raid_send_dot_remove(sd);
	raid_id = sd->state.raid_id;
	sd->state.raid_id = 0;

	//raid_tally_stats(sd);
	//memset(&sd->status.raid_round_stats, 0, sizeof(sd->status.raid_round_stats));

	//sd->state.raid_afk = 0;
	sd->state.rmaster_flag = NULL;
	raid_member_removeskulls(sd);

	if( sd->status.guild_id && (g = guild_search(sd->status.guild_id)) != NULL )
	{ // Refresh Guild Information
		clif_guild_belonginfo(sd, g);
		clif_guild_basicinfo(sd);
		clif_guild_allianceinfo(sd);
		clif_guild_memberlist(sd);
		clif_guild_skillinfo(sd);
		clif_guild_emblem(sd, g);
	}

	clif_charnameupdate(sd);
	clif_guild_emblem_area(&sd->bl);

	if( (rd = raid_team_search(raid_id)) == NULL )
		return 0;

	ARR_FIND(0, MAX_RAID_MEMBERS, i, rd->members[i].sd == sd);
	if( i < MAX_RAID_MEMBERS ) // Removes member from raid
		memset(&rd->members[i], 0, sizeof(rd->members[0]));
	if( rd->leader_char_id == sd->status.char_id )
		rd->leader_char_id = 0;


	rd->count--;
	switch( flag )
	{
		case 1: sprintf(output, "%s: %s has quit the raid.", rd->g->name, sd->status.name); break;
		case 0: sprintf(output, "%s: %s is leaving the raid.", rd->g->name, sd->status.name); break;
	}

	for( i = 0; i < MAX_RAID_MEMBERS; i++ )
	{ // Update other raid members
		if( (pl_sd = rd->members[i].sd) == NULL )
			continue;



 		clif_raid_message(rd, 0, rd->g->name, output, strlen(output) + 1);

		if( !rd->leader_char_id && pl_sd->status.char_id)
		{ // Set new Leader first on the list
			rd->leader_char_id = pl_sd->status.char_id;
			pl_sd->state.bmaster_flag = rd;
		}

		clif_guild_basicinfo(pl_sd);
		clif_raid_emblem(pl_sd, rd->g);
		clif_raid_memberlist(pl_sd);
	}

	if( rd->logout_event[0] && flag )
		npc_event(sd, rd->logout_event, 0);

	return rd->count;
}

int raid_member_respawn(struct map_session_data *sd)
{ // Respawn after killed
	struct raid_data *rd;
	if( sd == NULL || !pc_isdead(sd) || !sd->state.raid_id || (rd = raid_team_search(sd->state.raid_id)) == NULL )
		return 0;
	if( rd->mapindex == 0 )
		return 0; // Respawn not handled by Core
	pc_setpos(sd, rd->mapindex, rd->x, rd->y, CLR_OUTSIGHT);
	status_revive(&sd->bl, 1, 100);

	return 1; // Warped
}

struct guild* raid_guild_get(int raid_id)
{ // Return Fake Guild for raid Members
	struct raid_data *rd = raid_team_search(raid_id);
	if( rd == NULL ) return NULL;
	return rd->g;
}

int raid_create(unsigned short mapindex, short rx, short ry, int guild_index, const char *ev, const char *dev)
{
	struct raid_data *rd;
	int i;
	raid_team_counter++;

	CREATE(rd, struct raid_data, 1);
	rd->raid_id = raid_team_counter;
	rd->creation_tick = last_tick;
	rd->count = 0;
	rd->g = &raid_guild[guild_index];
	rd->mapindex = mapindex;
	rd->x = rx;
	rd->y = ry;
	safestrncpy(rd->logout_event, ev, sizeof(rd->logout_event));
	safestrncpy(rd->die_event, dev, sizeof(rd->die_event));
	for( i = 0; i < MAX_GUILDSKILL; i++ )
		rd->skill_block_timer[i] = INVALID_TIMER;

	memset(&rd->members, 0, sizeof(rd->members));
	rd->color = raid_colors[guild_index];

	idb_put(raid_team_db, raid_team_counter, rd);

	return rd->raid_id;
}

void do_init_raid(void)
{
	raid_team_db = idb_alloc(DB_OPT_RELEASE_DATA);

	add_timer_func_list(raid_send_xy_timer, "raid_send_xy_timer");
	add_timer_interval(gettick() + battle_config.raid_update_interval, raid_send_xy_timer, 0, 0, battle_config.raid_update_interval);
	raid_guild_build_data();

}

void do_final_battleground(void)
{
	raid_team_db->destroy(raid_team_db, NULL);
}

#endif