// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#ifndef	_VENDING_H_
#define	_VENDING_H_

#include "../common/cbasetypes.h"
//#include "map.h"
struct map_session_data;
struct s_search_store_search;


enum e_vend_currency
{
	VEND_CURRENCY_ZENY,
	VEND_CURRENCY_ITEM,
	VEND_CURRENCY_CASH,
	VEND_CURRENCY_KAFRA,

	VEND_CURRENCY_MAX
};

struct s_vend_emblem {
	short fake_guild_id;
	int emblem_len,emblem_id;
	char emblem_data[2048];
};

extern struct s_vend_emblem vend_emblems[VEND_CURRENCY_MAX];



struct s_vending {
	short index;
	short amount;
	unsigned int value;
};
struct s_vend_type {
	enum e_vend_currency currency;
	int itemid;
};

void vending_closevending(struct map_session_data* sd);
void vending_openvending(struct map_session_data* sd, const char* message, bool flag, const uint8* data, int count);
void vending_vendinglistreq(struct map_session_data* sd, int id);
void vending_purchasereq(struct map_session_data* sd, int aid, int uid, const uint8* data, int count);
bool vending_search(struct map_session_data* sd, unsigned short nameid);
bool vending_searchall(struct map_session_data* sd, const struct s_search_store_search* s);

void do_init_vending();

#endif /* _VENDING_H_ */
