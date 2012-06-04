// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#ifndef _STORAGE_H_
#define _STORAGE_H_

//#include "../common/mmo.h"
struct storage_data;
struct guild_storage;
struct member_storage_data;
struct item;
//#include "map.h"
struct map_session_data;
bool storage_haspassword(struct map_session_data *sd, int type);
bool storage_setpassword(struct map_session_data *sd, const char* passwd, int type); 
bool storage_checkpassword(struct map_session_data *sd, const char* passwd, int type);

static int storage_additem(struct map_session_data* sd, struct item* item_data, int amount, int flag);

int storage_delitem(struct map_session_data* sd, int n, int amount);
int storage_storageopen(struct map_session_data *sd);
int storage_storageadd(struct map_session_data *sd,int index,int amount);
int storage_storageget(struct map_session_data *sd,int index,int amount);
int storage_storageaddfromcart(struct map_session_data *sd,int index,int amount);
int storage_storagegettocart(struct map_session_data *sd,int index,int amount);
void storage_storageclose(struct map_session_data *sd);
int do_init_storage(void);
void do_final_storage(void);
void do_reconnect_storage(void);
void storage_storage_quit(struct map_session_data *sd, int flag);
struct member_storage_data* member2storage(int member_id);
struct guild_storage* guild2storage(int guild_id);
int member_storage_delete(int member_id);
int guild_storage_delete(int guild_id);
int storage_member_storageopen(struct map_session_data *sd);
int storage_guild_storageopen(struct map_session_data *sd);

int member_storage_additem(struct map_session_data *sd, struct member_storage_data *mstor, struct item *item_data, int amount);
int guild_storage_additem(struct map_session_data *sd,struct guild_storage *stor,struct item *item_data,int amount);
int member_storage_delitem(struct map_session_data *sd,struct member_storage_data *mstor,int n,int amount);
int guild_storage_delitem(struct map_session_data *sd,struct guild_storage *stor,int n,int amount);
int storage_member_storageadd(struct map_session_data *sd,int index,int amount);
int storage_guild_storageadd(struct map_session_data *sd,int index,int amount);
int storage_member_storageget(struct map_session_data *sd,int index,int amount);
int storage_guild_storageget(struct map_session_data *sd,int index,int amount);
int storage_member_storageaddfromcart(struct map_session_data *sd,int index,int amount);
int storage_guild_storageaddfromcart(struct map_session_data *sd,int index,int amount);
int storage_member_storagegettocart(struct map_session_data *sd,int index,int amount);
int storage_guild_storagegettocart(struct map_session_data *sd,int index,int amount);
int storage_member_storageclose(struct map_session_data *sd);
int storage_guild_storageclose(struct map_session_data *sd);
int storage_member_storage_quit(struct map_session_data *sd,int flag);
int storage_guild_storage_quit(struct map_session_data *sd,int flag);
int storage_member_storagesave(int account_id, int member_id, int flag);
int storage_guild_storagesave(int account_id, int guild_id, int flag);
int storage_member_storagesaved(int member_id);
int storage_guild_storagesaved(int guild_id); //Ack from char server that guild store was saved.


int compare_item(struct item *a, struct item *b);
int storage_additem2(struct map_session_data *sd, struct item* item_data, int amount);

#endif /* _STORAGE_H_ */
