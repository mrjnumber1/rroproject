// Copyright (c) mrjs butt - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#ifndef _RAID_H_
#define _RAID_H_

#include "../common/mmo.h" // struct party
#include "guild.h"

#define MAX_RAID_MEMBERS MAX_PARTY

struct raid_member_data 
{
	unsigned short x, y;
	struct map_session_data *sd;
};

struct raid_data 
{
	unsigned int raid_id;
	time_t creation_tick; // Creation of this Team
	unsigned char count;
	struct raid_member_data members[MAX_RAID_MEMBERS];

	// Team Leader and BG Skills features
	int leader_char_id;
	int skill_block_timer[MAX_GUILDSKILL];
	unsigned int color;

	// Fake Guild Link
	struct guild *g;

	// Cementery
	unsigned short mapindex, x, y;
	bool reveal_pos, reveal_flag;

	// Script Events
	char logout_event[50];
	char die_event[50];

	// Score Board
	int team_score;
};

extern struct guild raid_guild[];

struct raid_data* raid_team_search(int raid_id);
struct guild* raid_guild_get(int raid_id);
int raid_send_dot_remove(struct map_session_data *sd);
int raid_team_get_id(struct block_list *bl);
struct map_session_data* raid_getavailablesd(struct raid_data *bg);

int raid_create(unsigned short mapindex, short rx, short ry, int guild_index, const char *ev, const char *dev);
int raid_team_join(int raid_id, struct map_session_data *sd);
int raid_team_clean(int raid_id, bool remove);
int raid_team_delete(int raid_id);
int raid_team_leave(struct map_session_data *sd, int flag);
int raid_team_warp(int raid_id, unsigned short mapindex, short x, short y);
int raid_member_respawn(struct map_session_data *sd);
int raid_send_message(struct map_session_data *sd, const char *mes, int len);
// void raid_randomize_teams(int raid_id1, int raid_id2);
void raid_team_getitem(int raid_id, int nameid, int amount);
void raid_team_get_kafrapoints(int raid_id, int amount);
void raid_team_rewards(int raid_id, int nameid, int amount, int kafrapoints, int quest_id, const char *var, int add_value, int raid_arena, int raid_result);
int raid_checkskill(struct raid_data *raid, int id);
void raid_block_skill_status(struct raid_data *raid, int skillnum);
void raid_block_skill_start(struct raid_data *raid, int skillnum, int time);


void do_init_raid(void);
void do_final_raid(void);

#endif /* _RAID_H_ */