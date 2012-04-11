// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#ifndef _QUEST_H_
#define _QUEST_H_

struct s_quest_db {
	int id;
	unsigned int time;
	int mob[MAX_QUEST_OBJECTIVES];
	int count[MAX_QUEST_OBJECTIVES];
	int num_objectives;
	//char name[NAME_LENGTH];
};
extern struct s_quest_db quest_db[MAX_QUEST_DB];

#define MISSION_MAX_MOBS 6
#define MISSION_MAX_ITEMS 6
#define MISSION_MAX_REWARDS 5
#define MAX_MISSION_DB 255

struct s_mission_db {
	int id;
	unsigned int cooldown;
	unsigned int base_exp, job_exp;
	unsigned short points;
	unsigned int mapindex;
	int min_level, max_level;
	int exp_multiplier; 
	char* name;

	struct
	{
		unsigned short id;
		unsigned short count;
	} mob[MISSION_MAX_MOBS], item[MISSION_MAX_ITEMS], reward[MISSION_MAX_REWARDS];
	
};
extern struct s_mission_db mission_db[MAX_MISSION_DB];
enum mission_status 
{ 
	MISSION_NOT_FOUND = -1, 
	MISSION_HUNTING_INCOMPLETE, 
	MISSION_HUNTING_COMPLETE, 
	MISSION_GATHERING_INCOMPLETE,
	MISSION_GATHERING_COMPLETE,
	MISSION_COMPLETE,
	MISSION_INCOMPLETE,
	MISSION_AVAILABLE_SLOT,
};

typedef enum quest_check_type { HAVEQUEST, PLAYTIME, HUNTING } quest_check_type;

int quest_pc_login(TBL_PC * sd);

int quest_add(TBL_PC * sd, int quest_id);
int quest_delete(TBL_PC * sd, int quest_id);
int quest_change(TBL_PC * sd, int qid1, int qid2);


int mission_check_status(struct map_session_data *sd, int i);
int mission_check_gathering_status(struct map_session_data *sd, int i);
int mission_check_hunting_status(struct map_session_data *sd, int i);
int mission_update_sub(struct block_list *bl, va_list ap);
int mission_update(struct map_session_data *sd, int mob_id);


int quest_update_objective_sub(struct block_list *bl, va_list ap);
void quest_update_objective(TBL_PC * sd, int mob);
int quest_update_status(TBL_PC * sd, int quest_id, quest_state status);
int quest_check(TBL_PC * sd, int quest_id, quest_check_type type);

int quest_search_db(int quest_id);

void do_init_quest();

void do_final_quest();

#endif
