// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#include "../common/cbasetypes.h"
#include "../common/db.h"
#include "../common/nullpo.h"
#include "../common/malloc.h"
#include "../common/showmsg.h"
#include "../common/utils.h"
#include "../common/md5calc.h"
#include "../common/strlib.h"

#include "map.h" // struct map_session_data
#include "storage.h"
#include "chrif.h"
#include "itemdb.h"
#include "clif.h"
#include "intif.h"
#include "pc.h"
#include "guild.h"
#include "battle.h"
#include "battleground.h"
#include "atcommand.h"
#include "log.h"

#include <stdio.h>
#include <stdlib.h>
#include <string.h>


static DBMap* guild_storage_db; // int guild_id -> struct guild_storage*
static DBMap* member_storage_db;

/*==========================================
 * 倉庫内アイテムソート
 *------------------------------------------*/
static int storage_comp_item(const void *_i1, const void *_i2)
{
	struct item *i1 = (struct item *)_i1;
	struct item *i2 = (struct item *)_i2;

	if (i1->nameid == i2->nameid)
		return 0;
	else if (!(i1->nameid) || !(i1->amount))
		return 1;
	else if (!(i2->nameid) || !(i2->amount))
		return -1;
	return i1->nameid - i2->nameid;
}

static void storage_sortitem(struct item* items, unsigned int size)
{
	nullpo_retv(items);

	if( battle_config.client_sort_storage )
	{
		qsort(items, size, sizeof(struct item), storage_comp_item);
	}
}

/*==========================================
 * 初期化とか
 *------------------------------------------*/
int do_init_storage(void) // map.c::do_init()から呼ばれる
{
	guild_storage_db=idb_alloc(DB_OPT_RELEASE_DATA);
	member_storage_db=idb_alloc(DB_OPT_RELEASE_DATA);
	return 1;
}
void do_final_storage(void) // by [MC Cameri]
{
	guild_storage_db->destroy(guild_storage_db,NULL);
	member_storage_db->destroy(member_storage_db,NULL);
}


static int storage_reconnect_sub(DBKey key,void *data,va_list ap)
{ //Parses storage and saves 'dirty' ones upon reconnect. [Skotlex]

	struct guild_storage* stor = (struct guild_storage*) data;
	if (stor->dirty && stor->storage_status == 0) //Save closed storages.
		storage_guild_storagesave(0, stor->guild_id,0);

	return 0;
}
static int storage_member_reconnect_sub(DBKey key,void *data, va_list ap)
{
	struct member_storage_data* mstor = (struct member_storage_data*) data;
	if(mstor->dirty && mstor->storage_status == 0)
		storage_member_storagesave(0, mstor->member_id, 0);
	
	return 0;
}

//Function to be invoked upon server reconnection to char. To save all 'dirty' storages [Skotlex]
void do_reconnect_storage(void)
{
	guild_storage_db->foreach(guild_storage_db, storage_reconnect_sub);
	member_storage_db->foreach(member_storage_db, storage_member_reconnect_sub);
}

/*==========================================
 * Opens a storage. Returns:
 * 0 - success
 * 1 - fail
 *------------------------------------------*/
int storage_storageopen(struct map_session_data *sd)
{
	nullpo_ret(sd);

	if(sd->state.storage_flag)
		return 1; //Already open?
	
	if( !pc_can_give_items(pc_isGM(sd)) )
  	{ //check is this GM level is allowed to put items to storage
		clif_displaymessage(sd->fd, msg_txt(246));
		return 1;
	}
	
	sd->state.storage_flag = 1;
	storage_sortitem(sd->status.storage.items, ARRAYLENGTH(sd->status.storage.items));
	clif_storagelist(sd, sd->status.storage.items, ARRAYLENGTH(sd->status.storage.items));
	clif_updatestorageamount(sd, sd->status.storage.storage_amount, MAX_STORAGE);
	return 0;
}
// helper function
int compare_item(struct item *a, struct item *b)
{
	if( a->nameid == b->nameid &&
		a->identify == b->identify &&
		a->refine == b->refine &&
		a->attribute == b->attribute &&
		a->expire_time == b->expire_time &&
		a->serial == b->serial)
	{
		int i;
		for (i = 0; i < MAX_SLOTS && (a->card[i] == b->card[i]); i++);
		return (i == MAX_SLOTS);
	}
	return 0;
}

/*==========================================
 * Internal del-item function
 *------------------------------------------*/
int storage_delitem(struct map_session_data* sd, int n, int amount)
{
	if( sd->status.storage.items[n].nameid == 0 || sd->status.storage.items[n].amount < amount )
		return 1;

	sd->status.storage.items[n].amount -= amount;

	log_pick_pc(sd, LOG_TYPE_STORAGE, sd->status.storage.items[n].nameid, amount, &sd->status.storage.items[n], sd->status.storage.items[n].serial);

	if( sd->status.storage.items[n].amount == 0 )
	{
		memset(&sd->status.storage.items[n],0,sizeof(sd->status.storage.items[0]));
		sd->status.storage.storage_amount--;
		if( sd->state.storage_flag == 1 ) clif_updatestorageamount(sd, sd->status.storage.storage_amount, MAX_STORAGE);
	}
	if( sd->state.storage_flag == 1 ) clif_storageitemremoved(sd,n,amount);
	return 0;
}

/*==========================================
 * Add an item to the storage from the inventory.
 *------------------------------------------*/
int storage_storageadd(struct map_session_data* sd, int index, int amount)
{
	nullpo_ret(sd);

	if( sd->status.storage.storage_amount > MAX_STORAGE )
		return 0; // storage full

	if( index < 0 || index >= MAX_INVENTORY )
		return 0;

	if( sd->status.inventory[index].nameid <= 0 )
		return 0; // No item on that spot

	if( amount < 1 || amount > sd->status.inventory[index].amount )
  		return 0;

	if( storage_additem(sd,&sd->status.inventory[index],amount, 1) == 0 )
		pc_delitem(sd,index,amount,0,4);

	return 1;
}

/*==========================================
 * Retrieve an item from the storage.
 *------------------------------------------*/
int storage_storageget(struct map_session_data* sd, int index, int amount)
{
	int flag;

	if( index < 0 || index >= MAX_STORAGE )
		return 0;

	if( sd->status.storage.items[index].nameid <= 0 )
		return 0; //Nothing there
	
	if( amount < 1 || amount > sd->status.storage.items[index].amount )
		return 0;

	if( (flag = pc_additem(sd,&sd->status.storage.items[index],amount)) == 0 )
		storage_delitem(sd,index,amount);
	else
		clif_additem(sd,0,0,flag);

	return 1;
}

/*==========================================
 * Move an item from cart to storage.
 *------------------------------------------*/
int storage_storageaddfromcart(struct map_session_data* sd, int index, int amount)
{
	nullpo_ret(sd);

	if( sd->status.storage.storage_amount > MAX_STORAGE )
  		return 0; // storage full / storage closed

	if( index < 0 || index >= MAX_CART )
  		return 0;

	if( sd->status.cart[index].nameid <= 0 )
		return 0; //No item there.
	
	if( amount < 1 || amount > sd->status.cart[index].amount )
		return 0;

	if( storage_additem(sd,&sd->status.cart[index],amount, 1) == 0 )
		pc_cart_delitem(sd,index,amount,0);

	return 1;
}

/*==========================================
 * Get from Storage to the Cart
 *------------------------------------------*/
int storage_storagegettocart(struct map_session_data* sd, int index, int amount)
{
	nullpo_ret(sd);

	if( index < 0 || index >= MAX_STORAGE )
		return 0;
	
	if( sd->status.storage.items[index].nameid <= 0 )
		return 0; //Nothing there.
	
	if( amount < 1 || amount > sd->status.storage.items[index].amount )
		return 0;
	
	if( pc_cart_additem(sd,&sd->status.storage.items[index],amount) == 0 )
		storage_delitem(sd,index,amount);

	return 1;
}


/*==========================================
 * Modified By Valaris to save upon closing [massdriller]
 *------------------------------------------*/
void storage_storageclose(struct map_session_data* sd)
{
	nullpo_retv(sd);

	clif_storageclose(sd);

	if( save_settings&4 )
		chrif_save(sd,0); //Invokes the storage saving as well.

	sd->state.storage_flag = 0;
}

/*==========================================
 * When quitting the game.
 *------------------------------------------*/
void storage_storage_quit(struct map_session_data* sd, int flag)
{
	nullpo_retv(sd);
	
	if (save_settings&4)
		chrif_save(sd, flag); //Invokes the storage saving as well.

	sd->state.storage_flag = 0;
}

static void* create_memberstorage(DBKey key, va_list args)
{
	struct member_storage_data *ms = NULL;

	ms = (struct member_storage_data*) aCalloc(sizeof(struct member_storage_data),1);
	ms->member_id=key.i;

	return ms;
}
static void* create_guildstorage(DBKey key, va_list args)
{
	struct guild_storage *gs = NULL;

	gs = (struct guild_storage *) aCalloc(sizeof(struct guild_storage), 1);
	gs->guild_id=key.i;

	return gs;
}
struct member_storage_data * member2storage(int member_id)
{
	struct member_storage_data *ms = NULL;

	if(member_id != 0)
		ms = (struct member_storage_data *) idb_ensure(member_storage_db, member_id, create_memberstorage);

	return ms;
}
struct guild_storage *guild2storage(int guild_id)
{
	struct guild_storage *gs = NULL;
	if(guild_search(guild_id) != NULL)
		gs=(struct guild_storage *) idb_ensure(guild_storage_db,guild_id,create_guildstorage);
	return gs;
}

struct member_storage_data* member2storage2(int member_id)
{
	return (struct member_storage_data*)idb_get(member_storage_db, member_id);
}

struct guild_storage *guild2storage2(int guild_id)
{	//For just locating a storage without creating one. [Skotlex]
	return (struct guild_storage*)idb_get(guild_storage_db,guild_id);
}

int guild_storage_delete(int guild_id)	
{
	idb_remove(guild_storage_db,guild_id);
	return 0;
}

int storage_member_storageopen(struct map_session_data* sd)
{
	struct member_storage_data *mstor;

	nullpo_ret(sd);

	if(sd->status.member_id <= 0)
		return 2;

	if(sd->state.storage_flag)
		return 1;

	if( !pc_can_give_items(pc_isGM(sd)) )
	{
		clif_displaymessage(sd->fd, msg_txt(246));
		return 1;
	}

	if((mstor = member2storage2(sd->status.member_id)) == NULL)
	{
		intif_request_member_storage(sd->status.account_id, sd->status.member_id);
		return 0;
	}
	if(mstor->storage_status)
		return 1;

	mstor->storage_status = 1;
	sd->state.storage_flag = 3;
	storage_sortitem(mstor->items, ARRAYLENGTH(mstor->items));
	clif_storagelist(sd, mstor->items, ARRAYLENGTH(mstor->items));
	clif_updatestorageamount(sd, mstor->storage_amount, MAX_MEMBER_STORAGE);
	return 0;
}

int storage_guild_storageopen(struct map_session_data* sd)
{
	struct guild_storage *gstor;

	nullpo_ret(sd);

	if(sd->status.guild_id <= 0)
		return 2;

	if(sd->state.storage_flag)
		return 1; //Can't open both storages at a time.
	
	if( !pc_can_give_items(pc_isGM(sd)) ) { //check is this GM level can open guild storage and store items [Lupus]
		clif_displaymessage(sd->fd, msg_txt(246));
		return 1;
	}

	if((gstor = guild2storage2(sd->status.guild_id)) == NULL) {
		intif_request_guild_storage(sd->status.account_id,sd->status.guild_id);
		return 0;
	}
	if(gstor->storage_status)
		return 1;
	
	gstor->storage_status = 1;
	sd->state.storage_flag = 2;
	storage_sortitem(gstor->items, ARRAYLENGTH(gstor->items));
	clif_storagelist(sd, gstor->items, ARRAYLENGTH(gstor->items));
	clif_updatestorageamount(sd, gstor->storage_amount, MAX_GUILD_STORAGE);
	return 0;
}

int member_storage_additem(struct map_session_data* sd, struct member_storage_data* mstor, struct item* item_data, int amount)
{
	struct item_data *data;
	int i;
	
	nullpo_retr(1, sd);
	nullpo_retr(1, mstor);
	nullpo_retr(1, item_data);

	if(item_data->nameid <= 0 || amount <= 0)
		return 1;

	data = itemdb_search(item_data->nameid);

	if( !itemdb_canmemberstore(item_data, pc_isGM(sd)) || item_data->expire_time )
	{
		clif_displaymessage(sd->fd, msg_txt(264));
		return 1;
	}

	if( !pc_can_dropitems(sd) )
		return 1;

	if( item_data->card[0] == CARD0_CREATE )
	{
		int char_id = MakeDWord(item_data->card[2], item_data->card[3]);
		if( (BG_CHARID && char_id == BG_CHARID && BG_TRADE&128) || (WOE_CHARID && char_id == WOE_CHARID && WOE_TRADE&128) || (RAID_CHARID && char_id == RAID_CHARID && RAID_TRADE&128) )
		{
			clif_displaymessage(sd->fd, msg_txt(264));
			return 1;
		}
	}

	if( itemdb_isstackable2(data))
	{
		for(i=0; i< MAX_MEMBER_STORAGE; ++i)
		{
			if(compare_item(&mstor->items[i], item_data))
			{
				if(mstor->items[i].amount+amount > MAX_AMOUNT)
					return 1;

				mstor->items[i].amount += amount;
				clif_storageitemadded(sd, &mstor->items[i], i, amount);
				mstor->dirty = 1;


				log_pick_pc(sd, LOG_TYPE_MEMBER_STORAGE, item_data->nameid, -amount, item_data, item_data->serial);

				return 0;
			}
		}
	}

	//Add item
	for(i=0;i<MAX_MEMBER_STORAGE && mstor->items[i].nameid;i++);
	
	if(i>=MAX_MEMBER_STORAGE)
		return 1;
	
	memcpy(&mstor->items[i],item_data,sizeof(mstor->items[0]));

	mstor->items[i].amount=amount;
	mstor->storage_amount++;

	clif_storageitemadded(sd,&mstor->items[i],i,amount);
	clif_updatestorageamount(sd, mstor->storage_amount, MAX_MEMBER_STORAGE);

	mstor->dirty = 1;


	log_pick_pc(sd, LOG_TYPE_MEMBER_STORAGE, item_data->nameid, -amount, item_data, item_data->serial);

	return 0;
}
int guild_storage_additem(struct map_session_data* sd, struct guild_storage* stor, struct item* item_data, int amount)
{
	struct item_data *data;
	int i;

	nullpo_retr(1, sd);
	nullpo_retr(1, stor);
	nullpo_retr(1, item_data);

	if(item_data->nameid <= 0 || amount <= 0)
		return 1;

	data = itemdb_search(item_data->nameid);

	if( !itemdb_canguildstore(item_data, pc_isGM(sd)) || item_data->expire_time )
	{	//Check if item is storable. [Skotlex]
		clif_displaymessage (sd->fd, msg_txt(264));
		return 1;
	}

	if( !pc_can_dropitems(sd) )
		return 1;

	if( item_data->card[0]==CARD0_CREATE ) 
	{
		int char_id = MakeDWord(item_data->card[2],item_data->card[3]);
		if( (BG_CHARID && char_id == BG_CHARID && BG_TRADE&64) || (WOE_CHARID && char_id == WOE_CHARID && WOE_TRADE&64) || (RAID_CHARID && char_id == RAID_CHARID && RAID_TRADE&64) )
		{
			clif_displaymessage (sd->fd, msg_txt(264));
			return 1;
		}
	}


	if(itemdb_isstackable2(data)){ //Stackable
		for(i=0;i<MAX_GUILD_STORAGE;i++){
			if(compare_item(&stor->items[i], item_data)) {
				if(stor->items[i].amount+amount > MAX_AMOUNT)
					return 1;
				stor->items[i].amount+=amount;
				clif_storageitemadded(sd,&stor->items[i],i,amount);
				stor->dirty = 1;
				log_pick_pc(sd,  LOG_TYPE_GSTORAGE, item_data->nameid, -amount, item_data, item_data->serial);
				return 0;
			}
		}
	}
	//Add item
	for(i=0;i<MAX_GUILD_STORAGE && stor->items[i].nameid;i++);
	
	if(i>=MAX_GUILD_STORAGE)
		return 1;
	
	memcpy(&stor->items[i],item_data,sizeof(stor->items[0]));
	stor->items[i].amount=amount;
	stor->storage_amount++;
	clif_storageitemadded(sd,&stor->items[i],i,amount);
	clif_updatestorageamount(sd, stor->storage_amount, MAX_GUILD_STORAGE);
	stor->dirty = 1;

	log_pick_pc(sd, LOG_TYPE_GSTORAGE, item_data->nameid, -amount, item_data, item_data->serial);

	return 0;
}

int member_storage_delitem(struct map_session_data* sd, struct member_storage_data* mstor, int n, int amount)
{
	nullpo_retr(1, sd);
	nullpo_retr(1, mstor);

	if(mstor->items[n].nameid==0 || mstor->items[n].amount < amount)
		return 1;

	mstor->items[n].amount -= amount;

	log_pick_pc(sd, LOG_TYPE_MEMBER_STORAGE, mstor->items[n].nameid, amount, &mstor->items[n], mstor->items[n].serial);

	if(mstor->items[n].amount == 0)
	{
		memset(&mstor->items[n], 0, sizeof(mstor->items[0]));
		mstor->storage_amount--;
		clif_updatestorageamount(sd, mstor->storage_amount, MAX_MEMBER_STORAGE);
	}

	clif_storageitemremoved(sd, n, amount);
	mstor->dirty = 1;

	return 0;
}

int guild_storage_delitem(struct map_session_data* sd, struct guild_storage* stor, int n, int amount)
{
	nullpo_retr(1, sd);
	nullpo_retr(1, stor);

	if(stor->items[n].nameid==0 || stor->items[n].amount<amount)
		return 1;

	stor->items[n].amount-=amount;

	log_pick_pc(sd, LOG_TYPE_GSTORAGE, stor->items[n].nameid, amount, &stor->items[n], stor->items[n].serial);

	if(stor->items[n].amount==0){
		memset(&stor->items[n],0,sizeof(stor->items[0]));
		stor->storage_amount--;
		clif_updatestorageamount(sd, stor->storage_amount, MAX_GUILD_STORAGE);
	}
	clif_storageitemremoved(sd,n,amount);
	stor->dirty = 1;
	return 0;
}

int storage_additem2(struct map_session_data *sd, struct item* item_data, int amount)
{
	nullpo_ret(sd);
	nullpo_ret(item_data);

	if( sd->status.storage.storage_amount > MAX_STORAGE )
		return 0;
	if( item_data->nameid <= 0 || amount <= 0 )
		return 0;
	if( amount > MAX_AMOUNT )
		return 0;
	if( storage_additem(sd,item_data,amount,0) == 0 )
		return 1;

	return 0;
}

/*==========================================
 * Internal add-item function.
 *------------------------------------------*/
static int storage_additem(struct map_session_data* sd, struct item* item_data, int amount, int flag)
{
	struct storage_data* stor = &sd->status.storage;
	struct item_data *data;
	int i;

	if( item_data->nameid <= 0 || amount <= 0 )
		return 1;
	
	data = itemdb_search(item_data->nameid);

	if( !itemdb_canstore(item_data, pc_isGM(sd)) )
	{	//Check if item is storable. [Skotlex]
		clif_displaymessage (sd->fd, msg_txt(264));
		return 1;
	}

	if ( item_data->expire_time)
	{
		clif_displaymessage (sd->fd, msg_txt(264));
		return 1;
	}

	if( item_data->card[0]==CARD0_CREATE ) 
	{
		int char_id = MakeDWord(item_data->card[2],item_data->card[3]);
		if( (BG_CHARID && char_id == BG_CHARID && BG_TRADE&32) || (WOE_CHARID && char_id == WOE_CHARID && WOE_TRADE&32) || (RAID_CHARID && char_id == RAID_CHARID && RAID_TRADE&32) )
		{
			clif_displaymessage (sd->fd, msg_txt(264));
			return 1;
		}
	}

	if( itemdb_isstackable2(data) )
	{//Stackable
		for( i = 0; i < MAX_STORAGE; i++ )
		{
			if( compare_item(&stor->items[i], item_data) )
			{// existing items found, stack them
				if( amount > MAX_AMOUNT - stor->items[i].amount )
					return 1;
				stor->items[i].amount += amount;
				if( flag ) clif_storageitemadded(sd,&stor->items[i],i,amount);
				
				log_pick_pc(sd, LOG_TYPE_STORAGE, item_data->nameid, -amount, item_data, 0);
				return 0;
			}
		}
	}

	// find free slot
	ARR_FIND( 0, MAX_STORAGE, i, stor->items[i].nameid == 0 );
	if( i >= MAX_STORAGE )
		return 1;

	// add item to slot
	memcpy(&stor->items[i],item_data,sizeof(stor->items[0]));
	stor->storage_amount++;
	stor->items[i].amount = amount;
	if( flag )
	{
		clif_storageitemadded(sd,&stor->items[i],i,amount);
		clif_updatestorageamount(sd,stor->storage_amount, MAX_STORAGE);
	}
	
	log_pick_pc(sd, LOG_TYPE_STORAGE, item_data->nameid, -amount, item_data, item_data->serial );

	return 0;
}


int storage_member_storageadd(struct map_session_data* sd, int index, int amount)
{
	struct member_storage_data *mstor;

	nullpo_ret(sd);
	nullpo_ret(mstor=member2storage2(sd->status.member_id));

	if( !mstor->storage_status || mstor->storage_amount > MAX_MEMBER_STORAGE )
		return 0;

	if( index < 0 || index >= MAX_INVENTORY )
		return 0;

	if( sd->status.inventory[index].nameid <= 0 )
		return 0;

	if(member_storage_additem(sd, mstor, &sd->status.inventory[index], amount) == 0)
		pc_delitem(sd, index, amount, 0, 4);

	return 1;
}

int storage_guild_storageadd(struct map_session_data* sd, int index, int amount)
{
	struct guild_storage *stor;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));
		
	if( !stor->storage_status || stor->storage_amount > MAX_GUILD_STORAGE )
		return 0;
	
	if( index<0 || index>=MAX_INVENTORY )
		return 0;

	if( sd->status.inventory[index].nameid <= 0 )
		return 0;
	
	if( amount < 1 || amount > sd->status.inventory[index].amount )
		return 0;

//	log_tostorage(sd, index, 1);
	if(guild_storage_additem(sd,stor,&sd->status.inventory[index],amount)==0)
		pc_delitem(sd,index,amount,0,4);

	return 1;
}

int storage_member_storageget(struct map_session_data* sd, int index, int amount)
{
	struct member_storage_data *mstor;
	int flag;

	nullpo_ret(sd);
	nullpo_ret(mstor=member2storage2(sd->status.member_id));

	if(!mstor->storage_status)
		return 0;

	if( index < 0 || index >= MAX_MEMBER_STORAGE)
		return 0;

	if(mstor->items[index].nameid <= 0)
		return 0;

	if(amount < 1 || amount > mstor->items[index].amount)
		return 0;

	if((flag = pc_additem(sd, &mstor->items[index], amount)) == 0)
		member_storage_delitem(sd, mstor, index, amount);
	else
		clif_additem(sd, 0, 0, flag);

	return 0;
}

int storage_guild_storageget(struct map_session_data* sd, int index, int amount)
{
	struct guild_storage *stor;
	int flag;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));

	if(!stor->storage_status)
  		return 0;
	
	if(index<0 || index>=MAX_GUILD_STORAGE)
		return 0;

	if(stor->items[index].nameid <= 0)
		return 0;
	
	if(amount < 1 || amount > stor->items[index].amount)
	  	return 0;

	if((flag = pc_additem(sd,&stor->items[index],amount)) == 0)
		guild_storage_delitem(sd,stor,index,amount);
	else
		clif_additem(sd,0,0,flag);
//	log_fromstorage(sd, index, 1);

	return 0;
}

int storage_member_storageaddfromcart(struct map_session_data* sd, int index, int amount)
{
	struct member_storage_data *mstor;

	nullpo_ret(sd);
	nullpo_ret(mstor=member2storage2(sd->status.member_id));

	if( !mstor->storage_status || mstor->storage_amount > MAX_MEMBER_STORAGE )
		return 0;

	if( index < 0 || index >= MAX_CART )
		return 0;

	if( amount < 1 || amount > sd->status.cart[index].amount )
		return 0;

	if(member_storage_additem(sd, mstor, &sd->status.cart[index], amount) == 0)
		pc_cart_delitem(sd, index, amount, 0);

	return 1;
}


int storage_guild_storageaddfromcart(struct map_session_data* sd, int index, int amount)
{
	struct guild_storage *stor;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));

	if( !stor->storage_status || stor->storage_amount > MAX_GUILD_STORAGE )
		return 0;

	if( index < 0 || index >= MAX_CART )
		return 0;

	if( sd->status.cart[index].nameid <= 0 )
		return 0;
	
	if( amount < 1 || amount > sd->status.cart[index].amount )
		return 0;

	if(guild_storage_additem(sd,stor,&sd->status.cart[index],amount)==0)
		pc_cart_delitem(sd,index,amount,0);

	return 1;
}

int storage_member_storagegettocart(struct map_session_data* sd, int index, int amount)
{
	struct member_storage_data *mstor;
	
	nullpo_ret(sd);
	nullpo_ret(mstor=member2storage2(sd->status.member_id));

	if( !mstor->storage_status )
		return 0;

	if(index < 0 || index >= MAX_MEMBER_STORAGE)
		return 0;

	if(mstor->items[index].nameid <= 0)
		return 0;

	if(amount < 1 || amount > mstor->items[index].amount)
		return 0;

	if(pc_cart_additem(sd, &mstor->items[index], amount) == 0)
		member_storage_delitem(sd, mstor, index, amount);

	return 1;
}


int storage_guild_storagegettocart(struct map_session_data* sd, int index, int amount)
{
	struct guild_storage *stor;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));

	if(!stor->storage_status)
	  	return 0;

	if(index<0 || index>=MAX_GUILD_STORAGE)
	  	return 0;
	
	if(stor->items[index].nameid<=0)
		return 0;
	
	if(amount < 1 || amount > stor->items[index].amount)
		return 0;

	if(pc_cart_additem(sd,&stor->items[index],amount)==0)
		guild_storage_delitem(sd,stor,index,amount);

	return 1;
}

int storage_member_storagesave(int account_id, int member_id, int flag)
{
	struct member_storage_data *mstor = member2storage2(member_id);

	if(mstor)
	{
		if(flag)
			mstor->storage_status = 0;
		if(mstor->dirty)
			intif_send_member_storage(account_id, mstor);
		return 1;
	}
	return 0;
}
int storage_guild_storagesave(int account_id, int guild_id, int flag)
{
	struct guild_storage *stor = guild2storage2(guild_id);

	if(stor)
	{
		if (flag) //Char quitting, close it.
			stor->storage_status = 0;
	 	if (stor->dirty)
			intif_send_guild_storage(account_id,stor);
		return 1;
	}
	return 0;
}

int storage_member_storagesaved(int member_id)
{
	struct member_storage_data *mstor;

	if((mstor=member2storage2(member_id)) != NULL)
	{
		if(mstor->dirty && mstor->storage_status == 0)
			mstor->dirty = 0;
		return 1;
	}
	return 0;
}
int storage_guild_storagesaved(int guild_id)
{
	struct guild_storage *stor;

	if((stor=guild2storage2(guild_id)) != NULL) {
		if (stor->dirty && stor->storage_status == 0)
		{	//Storage has been correctly saved.
			stor->dirty = 0;
		}
		return 1;
	}
	return 0;
}

int storage_member_storageclose(struct map_session_data* sd)
{
	struct member_storage_data *mstor;

	nullpo_ret(sd);
	nullpo_ret(mstor=member2storage2(sd->status.member_id));

	clif_storageclose(sd);

	if(mstor->storage_status)
	{
		if(save_settings&4)
			chrif_save(sd, 0);
		else
			storage_member_storagesave(sd->status.account_id, sd->status.member_id, 0);

		mstor->storage_status = 0;
	}
	sd->state.storage_flag = 0;

	return 0;
}

int storage_guild_storageclose(struct map_session_data* sd)
{
	struct guild_storage *stor;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));

	clif_storageclose(sd);
	if (stor->storage_status)
	{
		if (save_settings&4)
			chrif_save(sd, 0); //This one also saves the storage. [Skotlex]
		else
			storage_guild_storagesave(sd->status.account_id, sd->status.guild_id,0);
		stor->storage_status=0;
	}
	sd->state.storage_flag = 0;

	return 0;
}

int storage_member_storage_quit(struct map_session_data* sd, int flag)
{
	struct member_storage_data *mstor;

	nullpo_ret(sd);
	nullpo_ret(mstor = member2storage2(sd->status.member_id));

	if(flag)
	{
		sd->state.storage_flag = 0;
		mstor->storage_status = 0;
		
		clif_storageclose(sd);
		
		if(save_settings&4)
			chrif_save(sd,0);
		return 0;
	}

	if(mstor->storage_status)
	{
		if(save_settings&4)
			chrif_save(sd,0);
		else
			storage_member_storagesave(sd->status.account_id, sd->status.member_id,1);
	}
	sd->state.storage_flag = 0;
	mstor->storage_status = 0;
	return 0;
}

int storage_guild_storage_quit(struct map_session_data* sd, int flag)
{
	struct guild_storage *stor;

	nullpo_ret(sd);
	nullpo_ret(stor=guild2storage2(sd->status.guild_id));
	
	if(flag)
	{	//Only during a guild break flag is 1 (don't save storage)
		sd->state.storage_flag = 0;
		stor->storage_status = 0;
		clif_storageclose(sd);
		if (save_settings&4)
			chrif_save(sd,0);
		return 0;
	}

	if(stor->storage_status) {
		if (save_settings&4)
			chrif_save(sd,0);
		else
			storage_guild_storagesave(sd->status.account_id,sd->status.guild_id,1);
	}
	sd->state.storage_flag = 0;
	stor->storage_status = 0;

	return 0;
}

bool storage_haspassword(struct map_session_data *sd, int type)
{
	nullpo_retr(true, sd); // better to force a fail on a null sd

	switch (type)
	{
	case 1: // regular storage
		{
			return (sd->status.storage.password[0] != '\0');
		}
		break;
	case 2:
		{
			struct guild_storage *gstor;

			if (!sd->status.guild_id)
			{
				ShowWarning("storage_checkpassword: checking for guildless char (c:%d a:%d)\n", sd->status.char_id, sd->status.account_id);
				return false;
			}
			else if((gstor = guild2storage2(sd->status.guild_id)) == NULL) 
			{
				ShowWarning("storage_checkpassword: no guild storage data available (c:%d a:%d)\n", sd->status.char_id, sd->status.account_id);
				intif_request_guild_storage(sd->status.account_id, sd->status.guild_id);
				return false;
			}
			else
				return (gstor->password[0] != '\0');
		}
		break;
	case 3:
		{
			struct member_storage_data *mstor;

			if (!sd->status.member_id)
			{
				clif_displaymessage(sd->fd, "You must have a member ID to do this! Oh god what happened?!");
				return false;
			}
			else if((mstor = member2storage2(sd->status.member_id)) == NULL) 
			{
				intif_request_member_storage(sd->status.account_id, sd->status.member_id);
				return false;
			}
			else
				return (mstor->password[0] != '\0');
		}
		break;

	}

	return false;
}
bool storage_setpassword(struct map_session_data *sd, const char* passwd, int type)
{
	char md5buf1[32], md5buf2[64+1], md5buf3[32+1];

	nullpo_retr(false, sd);
	nullpo_retr(false, passwd);

	memset(md5buf1, '\0', sizeof (md5buf1));
	memset(md5buf2, '\0', sizeof (md5buf2));
	memset(md5buf2, '\0', sizeof (md5buf3));

	if (strlen(passwd) < 6)
	{
		clif_displaymessage(sd->fd, "Sorry, passwords must be 6 characters or longer");
		return false;
	}
	MD5_String(passwd, md5buf1);
	ShowDebug("Received pw: %s\n", passwd);

	switch (type)
	{
	case 1: // regular storage
		{
			ShowDebug("Hashing %s%s\n", md5buf1, battle_config.storage_salt);
			snprintf(md5buf2, sizeof(md5buf2), "%s%s", md5buf1, battle_config.storage_salt);
			ShowDebug("Hashing %s\n");
			MD5_String(md5buf2, md5buf3);
			ShowDebug("Stored pw as %s\n", md5buf3);

			safestrncpy(sd->status.storage.password, md5buf3, sizeof (sd->status.storage.password));
			return true;
		}
		break;
	case 2:
		{
			struct guild_storage *gstor;
			
			if (!sd->status.guild_id)
			{
				clif_displaymessage(sd->fd, "You must be part of a guild to use this function!");
				return false;
			}

			if (!sd->state.gmaster_flag)
			{
				clif_displaymessage(sd->fd, "Only the guild leader may use this function.");
				return false;
			}

			if((gstor = guild2storage2(sd->status.guild_id)) == NULL) 
			{
				intif_request_guild_storage(sd->status.account_id, sd->status.guild_id);
				return false;
			}

			snprintf(md5buf2, sizeof(md5buf2), "%s%s", md5buf1, battle_config.gstorage_salt);
			MD5_String(md5buf2, md5buf3);

			safestrncpy(gstor->password, md5buf3, sizeof (gstor->password));

			return true;
		}
		break;
	case 3:
		{
			struct member_storage_data *mstor;

			if (!sd->status.member_id)
			{
				clif_displaymessage(sd->fd, "You must have a member ID to do this! Oh god what happened?!");
				return false;
			}
			if((mstor = member2storage2(sd->status.member_id)) == NULL) 
			{
				intif_request_member_storage(sd->status.account_id, sd->status.member_id);
				return false;
			}

			snprintf(md5buf2, sizeof(md5buf2), "%s%s", md5buf1, battle_config.mstorage_salt);
			MD5_String(md5buf2, md5buf3);
			
			safestrncpy(mstor->password, md5buf3, sizeof (mstor->password));

			return true;
		}
		break;
	default:
		return false;
	}

	return false;
}

bool storage_checkpassword(struct map_session_data *sd, const char* passwd, int type)
{
	char md5buf1[32], md5buf2[64+1], md5buf3[32+1]; 
	char target[32+1];
	char* salt;

	memset(md5buf1, '\0', sizeof (md5buf1));
	memset(md5buf2, '\0', sizeof (md5buf2));
	memset(md5buf2, '\0', sizeof (md5buf3));
	memset(target, '\0', sizeof (target));

	nullpo_retr(false, sd);

	switch (type)
	{
	case 1: //regular storage
		salt = battle_config.storage_salt;
		safestrncpy(target, sd->status.storage.password, sizeof(target));
		break;
	case 2: //guild storage
		{
			struct guild_storage *gstor;
			salt = battle_config.gstorage_salt;

			if((gstor = guild2storage2(sd->status.guild_id)) == NULL) 
			{
				intif_request_guild_storage(sd->status.account_id,sd->status.guild_id);
				return false;
			}

			safestrncpy(target, gstor->password, sizeof(target));

		}
		break;
	case 3: //member storage
		{
			struct member_storage_data *mstor;
			salt = battle_config.mstorage_salt;

			if((mstor = member2storage2(sd->status.member_id)) == NULL) 
			{
				intif_request_member_storage(sd->status.account_id,sd->status.member_id);
				return false;
			}

			safestrncpy(target, mstor->password, sizeof(target));

		}
		break;
	default:
		return false;
	}

	if(strlen(target) == 0)
		return true;
	MD5_String(passwd, md5buf1);
	snprintf(md5buf2, sizeof(md5buf2), "%s%s", md5buf1, salt);
	MD5_String(md5buf2, md5buf3);


	return ( strncmp(md5buf3, target, sizeof (target)) == 0 );

}
