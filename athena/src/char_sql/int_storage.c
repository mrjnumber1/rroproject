// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#include "../common/mmo.h"
#include "../common/malloc.h"
#include "../common/showmsg.h"
#include "../common/socket.h"
#include "../common/strlib.h" // StringBuf
#include "../common/sql.h"
#include "char.h"
#include "inter.h"

#include <stdio.h>
#include <string.h>
#include <stdlib.h>


#define STORAGE_MEMINC	16

/// Save storage data to sql
int storage_tosql(int account_id, struct storage_data* p)
{	
	if (p->password != NULL)
	{
		if (SQL_ERROR == Sql_Query(sql_handle, "UPDATE `login` SET `storage_password` = '%s' WHERE `account_id` = '%d' LIMIT 1", p->password, account_id))
			Sql_ShowDebug(sql_handle);
	}
	memitemdata_to_sql(p->items, MAX_STORAGE, account_id, TABLE_STORAGE);
	return 0;
}

#ifndef TXT_SQL_CONVERT
/// Load storage data to mem
int storage_fromsql(int account_id, struct storage_data* p)
{
	StringBuf buf;
	struct item* item;
	char* data;
	int i;
	int j;

	memset(p, 0, sizeof(struct storage_data)); //clean up memory
	p->storage_amount = 0;

	// storage {`account_id`/`id`/`nameid`/`amount`/`equip`/`identify`/`refine`/`attribute`/`card0`/`card1`/`card2`/`card3`}
	StringBuf_Init(&buf);
	StringBuf_AppendStr(&buf, "SELECT `id`,`nameid`,`amount`,`equip`,`identify`,`refine`,`attribute`,`expire_time`,`serial`");
	for( j = 0; j < MAX_SLOTS; ++j )
		StringBuf_Printf(&buf, ",`card%d`", j);
	StringBuf_Printf(&buf, " FROM `%s` WHERE `account_id`='%d' ORDER BY `nameid`", storage_db, account_id);

	if( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	for( i = 0; i < MAX_STORAGE && SQL_SUCCESS == Sql_NextRow(sql_handle); ++i )
	{
		item = &p->items[i];
		Sql_GetData(sql_handle, 0, &data, NULL); item->id = atoi(data);
		Sql_GetData(sql_handle, 1, &data, NULL); item->nameid = atoi(data);
		Sql_GetData(sql_handle, 2, &data, NULL); item->amount = atoi(data);
		Sql_GetData(sql_handle, 3, &data, NULL); item->equip = atoi(data);
		Sql_GetData(sql_handle, 4, &data, NULL); item->identify = atoi(data);
		Sql_GetData(sql_handle, 5, &data, NULL); item->refine = atoi(data);
		Sql_GetData(sql_handle, 6, &data, NULL); item->attribute = atoi(data);
		Sql_GetData(sql_handle, 7, &data, NULL); item->expire_time = (unsigned int)atoi(data);
		Sql_GetData(sql_handle, 8, &data, NULL); item->serial = (unsigned int)atoi(data);
		for( j = 0; j < MAX_SLOTS; ++j )
		{
			Sql_GetData(sql_handle, 9+j, &data, NULL); item->card[j] = atoi(data);
		}
	}
	p->storage_amount = i;
	
	Sql_FreeResult(sql_handle);

	StringBuf_Init(&buf); //TODO: this doesn't synch ith the login db from the login server
	StringBuf_Printf(&buf, "SELECT `storage_password` FROM `login` WHERE `account_id`='%d'", account_id);

	if ( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	if( SQL_ERROR == Sql_NextRow(sql_handle) )
	{// no such entry
		Sql_ShowDebug(sql_handle);
	}
	else
	{
		Sql_GetData(sql_handle, 0, &data, NULL); safestrncpy(p->password, data, sizeof(p->password));
	}
	Sql_FreeResult(sql_handle);

	ShowInfo("storage load complete from DB - id: %d (total: %d)\n", account_id, p->storage_amount);
	return 1;
}
#endif //TXT_SQL_CONVERT

/// Save guild_storage data to sql
int guild_storage_tosql(int guild_id, struct guild_storage* p)
{
	if (p->password != NULL)
	{
		if (SQL_ERROR == Sql_Query(sql_handle, "UPDATE `guild` SET `storage_password` = '%s' WHERE `guild_id` = '%d' LIMIT 1", p->password, guild_id))
			Sql_ShowDebug(sql_handle);
	}
	memitemdata_to_sql(p->items, MAX_GUILD_STORAGE, guild_id, TABLE_GUILD_STORAGE);
	ShowInfo ("guild storage save to DB - guild: %d\n", guild_id);
	return 0;
}

#ifndef TXT_SQL_CONVERT
/// Load guild_storage data to mem
int guild_storage_fromsql(int guild_id, struct guild_storage* p)
{
	StringBuf buf;
	struct item* item;
	char* data;
	int i;
	int j;

	memset(p, 0, sizeof(struct guild_storage)); //clean up memory
	p->storage_amount = 0;
	p->guild_id = guild_id;

	// storage {`guild_id`/`id`/`nameid`/`amount`/`equip`/`identify`/`refine`/`attribute`/`card0`/`card1`/`card2`/`card3`}
	StringBuf_Init(&buf);
	StringBuf_AppendStr(&buf, "SELECT `id`,`nameid`,`amount`,`equip`,`identify`,`refine`,`attribute`,`serial`");
	for( j = 0; j < MAX_SLOTS; ++j )
		StringBuf_Printf(&buf, ",`card%d`", j);
	StringBuf_Printf(&buf, " FROM `%s` WHERE `guild_id`='%d' ORDER BY `nameid`", guild_storage_db, guild_id);

	if( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	for( i = 0; i < MAX_GUILD_STORAGE && SQL_SUCCESS == Sql_NextRow(sql_handle); ++i )
	{
		item = &p->items[i];
		Sql_GetData(sql_handle, 0, &data, NULL); item->id = atoi(data);
		Sql_GetData(sql_handle, 1, &data, NULL); item->nameid = atoi(data);
		Sql_GetData(sql_handle, 2, &data, NULL); item->amount = atoi(data);
		Sql_GetData(sql_handle, 3, &data, NULL); item->equip = atoi(data);
		Sql_GetData(sql_handle, 4, &data, NULL); item->identify = atoi(data);
		Sql_GetData(sql_handle, 5, &data, NULL); item->refine = atoi(data);
		Sql_GetData(sql_handle, 6, &data, NULL); item->attribute = atoi(data);
		Sql_GetData(sql_handle, 7, &data, NULL); item->serial = (unsigned int)atol(data);

		item->expire_time = 0;
		for( j = 0; j < MAX_SLOTS; ++j )
		{
			Sql_GetData(sql_handle, 8+j, &data, NULL); item->card[j] = atoi(data);
		}
	}
	p->storage_amount = i;
	
	Sql_FreeResult(sql_handle);

	StringBuf_Init(&buf);
	StringBuf_Printf(&buf, "SELECT `storage_password` FROM `%s` WHERE `guild_id`='%d'", guild_db, guild_id);

	if ( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	if( SQL_ERROR == Sql_NextRow(sql_handle) )
	{// no such entry
		Sql_ShowDebug(sql_handle);
	}
	else
	{
		Sql_GetData(sql_handle, 0, &data, NULL); safestrncpy(p->password, data, sizeof(p->password));
	}

	Sql_FreeResult(sql_handle);




	ShowInfo("guild storage load complete from DB - id: %d (total: %d)\n", guild_id, p->storage_amount);
	return 0;
}


int member_storage_tosql(int member_id, struct member_storage_data* p)
{
	if (p->password != NULL)
	{
		if (SQL_ERROR == Sql_Query(sql_handle, "UPDATE `members` SET `storage_password` = '%s' WHERE `member_id` = '%d' LIMIT 1", p->password, member_id))
			Sql_ShowDebug(sql_handle);
	}

	memitemdata_to_sql(p->items, MAX_MEMBER_STORAGE, member_id, TABLE_MEMBER_STORAGE);
	ShowInfo ("member storage saved to DB - member: %d\n", member_id);
	return 0;
}

int member_storage_fromsql(int member_id, struct member_storage_data* p)
{
	StringBuf buf;
	struct item* item;
	char* data;
	int i,j;

	memset(p, 0, sizeof(struct member_storage_data));
	p->storage_amount = 0;
	p->member_id = member_id;

	// storage {`member_id`/`id`/`nameid`/`amount`/`equip`/`identify`/`refine`/`attribute`/`card0`/`card1`/`card2`/`card3`}
	StringBuf_Init(&buf);
	StringBuf_AppendStr(&buf, "SELECT `id`,`nameid`,`amount`,`equip`,`identify`,`refine`,`attribute`,`serial`");
	for( j = 0; j < MAX_SLOTS; ++j )
		StringBuf_Printf(&buf, ",`card%d`", j);
	StringBuf_Printf(&buf, " FROM `%s` WHERE `member_id`='%d' ORDER BY `nameid`", member_storage_db, member_id);

	if( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	for( i = 0; i < MAX_MEMBER_STORAGE && SQL_SUCCESS == Sql_NextRow(sql_handle); ++i )
	{
		item = &p->items[i];
		Sql_GetData(sql_handle, 0, &data, NULL); item->id = atoi(data);
		Sql_GetData(sql_handle, 1, &data, NULL); item->nameid = atoi(data);
		Sql_GetData(sql_handle, 2, &data, NULL); item->amount = atoi(data);
		Sql_GetData(sql_handle, 3, &data, NULL); item->equip = atoi(data);
		Sql_GetData(sql_handle, 4, &data, NULL); item->identify = atoi(data);
		Sql_GetData(sql_handle, 5, &data, NULL); item->refine = atoi(data);
		Sql_GetData(sql_handle, 6, &data, NULL); item->attribute = atoi(data);
		Sql_GetData(sql_handle, 7, &data, NULL); item->serial = (unsigned int)atol(data);
		item->expire_time = 0;
		for( j = 0; j < MAX_SLOTS; ++j )
		{
			Sql_GetData(sql_handle, 8+j, &data, NULL); item->card[j] = atoi(data);
		}
	}
	p->storage_amount = i;
	
	Sql_FreeResult(sql_handle);

	StringBuf_Init(&buf);
	StringBuf_Printf(&buf, "SELECT `storage_password` FROM `%s` WHERE `member_id`='%d'", member_db, member_id);

	if ( SQL_ERROR == Sql_Query(sql_handle, StringBuf_Value(&buf)) )
		Sql_ShowDebug(sql_handle);

	StringBuf_Destroy(&buf);

	if( SQL_ERROR == Sql_NextRow(sql_handle) )
	{// no such entry
		Sql_ShowDebug(sql_handle);
	}
	else
	{
		Sql_GetData(sql_handle, 0, &data, NULL); safestrncpy(p->password, data, sizeof(p->password));
	}
	Sql_FreeResult(sql_handle);



	ShowInfo("member storage load complete from DB - id: %d (total: %d)\n", member_id, p->storage_amount);
	return 0;
}

//---------------------------------------------------------
// storage data initialize
int inter_storage_sql_init(void)
{
	return 1;
}
// storage data finalize
void inter_storage_sql_final(void)
{
	return;
}

// q?f[^?
int inter_storage_delete(int account_id)
{
	if( SQL_ERROR == Sql_Query(sql_handle, "DELETE FROM `%s` WHERE `account_id`='%d'", storage_db, account_id) )
		Sql_ShowDebug(sql_handle);
	return 0;
}
int inter_guild_storage_delete(int guild_id)
{
	if( SQL_ERROR == Sql_Query(sql_handle, "DELETE FROM `%s` WHERE `guild_id`='%d'", guild_storage_db, guild_id) )
		Sql_ShowDebug(sql_handle);
	return 0;
}
int inter_member_storage_delete(int member_id)
{
	if( SQL_ERROR == Sql_Query(sql_handle, "DELETE FROM `%s` WHERE `member_id` = '%d'", member_storage_db, member_id) )
		Sql_ShowDebug(sql_handle);
	return 0;
}

//---------------------------------------------------------
// packet from map server
int mapif_load_member_storage(int fd, int account_id, int member_id)
{
	if( SQL_ERROR == Sql_Query(sql_handle, "SELECT `member_id` FROM `%s` WHERE `member_id`='%d'", member_db, member_id) )
		Sql_ShowDebug(sql_handle);
	else if( Sql_NumRows(sql_handle) > 0 )
	{ //mem exists
		WFIFOHEAD(fd, sizeof(struct member_storage_data)+12);
		WFIFOW(fd,0) = 0x3816;
		WFIFOW(fd,2) = sizeof(struct member_storage_data)+12;
		WFIFOL(fd,4) = account_id;
		WFIFOL(fd,8) = member_id;
		member_storage_fromsql(member_id, (struct member_storage_data*)WFIFOP(fd,12));
		WFIFOSET(fd, WFIFOW(fd,2));
		return 0;
	}
	Sql_FreeResult(sql_handle);
	WFIFOHEAD(fd, 12);
	WFIFOW(fd,0) = 0x3816;
	WFIFOW(fd,2) = 12;
	WFIFOL(fd,4) = account_id;
	WFIFOL(fd,8) = 0;
	WFIFOSET(fd, 12);
	return 0;
}

int mapif_save_member_storage_ack(int fd,int account_id,int member_id,int fail)
{
	WFIFOHEAD(fd,11);
	WFIFOW(fd,0)=0x3817;
	WFIFOL(fd,2)=account_id;
	WFIFOL(fd,6)=member_id;
	WFIFOB(fd,10)=fail;
	WFIFOSET(fd,11);
	return 0;
}


int mapif_load_guild_storage(int fd,int account_id,int guild_id)
{
	if( SQL_ERROR == Sql_Query(sql_handle, "SELECT `guild_id` FROM `%s` WHERE `guild_id`='%d'", guild_db, guild_id) )
		Sql_ShowDebug(sql_handle);
	else if( Sql_NumRows(sql_handle) > 0 )
	{// guild exists
		WFIFOHEAD(fd, sizeof(struct guild_storage)+12);
		WFIFOW(fd,0) = 0x3818;
		WFIFOW(fd,2) = sizeof(struct guild_storage)+12;
		WFIFOL(fd,4) = account_id;
		WFIFOL(fd,8) = guild_id;
		guild_storage_fromsql(guild_id, (struct guild_storage*)WFIFOP(fd,12));
		WFIFOSET(fd, WFIFOW(fd,2));
		return 0;
	}
	// guild does not exist
	Sql_FreeResult(sql_handle);
	WFIFOHEAD(fd, 12);
	WFIFOW(fd,0) = 0x3818;
	WFIFOW(fd,2) = 12;
	WFIFOL(fd,4) = account_id;
	WFIFOL(fd,8) = 0;
	WFIFOSET(fd, 12);
	return 0;
}
int mapif_save_guild_storage_ack(int fd,int account_id,int guild_id,int fail)
{
	WFIFOHEAD(fd,11);
	WFIFOW(fd,0)=0x3819;
	WFIFOL(fd,2)=account_id;
	WFIFOL(fd,6)=guild_id;
	WFIFOB(fd,10)=fail;
	WFIFOSET(fd,11);
	return 0;
}

//---------------------------------------------------------
// packet from map server
int mapif_parse_LoadMemberStorage(int fd)
{
	RFIFOHEAD(fd);
	mapif_load_member_storage(fd,RFIFOL(fd,2),RFIFOL(fd,6));
	return 0;
}
int mapif_parse_SaveMemberStorage(int fd)
{
	int member_id;
	int len;

	RFIFOHEAD(fd);
	member_id = RFIFOL(fd,8);
	len = RFIFOW(fd,2);

	if( sizeof(struct member_storage_data) != len - 12 )
	{
		ShowError("inter storage: data size error %d != %d\n", sizeof(struct member_storage_data), len-12);
	}
	else
	{
		if( SQL_ERROR == Sql_Query(sql_handle, "SELECT `member_id` FROM `%s` WHERE `member_id`='%d'", member_db, member_id) )
			Sql_ShowDebug(sql_handle);
		else if( Sql_NumRows(sql_handle) > 0 )
		{
			Sql_FreeResult(sql_handle);
			member_storage_tosql(member_id, (struct member_storage_data*)RFIFOP(fd,12));
			mapif_save_member_storage_ack(fd, RFIFOL(fd,4), member_id, 0);
			return 0;
		}
		Sql_FreeResult(sql_handle);
	}
	mapif_save_member_storage_ack(fd, RFIFOL(fd,4), member_id, 1);
	return 0;
}
int mapif_parse_LoadGuildStorage(int fd)
{
	RFIFOHEAD(fd);
	mapif_load_guild_storage(fd,RFIFOL(fd,2),RFIFOL(fd,6));
	return 0;
}

int mapif_parse_SaveGuildStorage(int fd)
{
	int guild_id;
	int len;

	RFIFOHEAD(fd);
	guild_id = RFIFOL(fd,8);
	len = RFIFOW(fd,2);

	if( sizeof(struct guild_storage) != len - 12 )
	{
		ShowError("inter storage: data size error %d != %d\n", sizeof(struct guild_storage), len - 12);
	}
	else
	{
		if( SQL_ERROR == Sql_Query(sql_handle, "SELECT `guild_id` FROM `%s` WHERE `guild_id`='%d'", guild_db, guild_id) )
			Sql_ShowDebug(sql_handle);
		else if( Sql_NumRows(sql_handle) > 0 )
		{// guild exists
			Sql_FreeResult(sql_handle);
			guild_storage_tosql(guild_id, (struct guild_storage*)RFIFOP(fd,12));
			mapif_save_guild_storage_ack(fd, RFIFOL(fd,4), guild_id, 0);
			return 0;
		}
		Sql_FreeResult(sql_handle);
	}
	mapif_save_guild_storage_ack(fd, RFIFOL(fd,4), guild_id, 1);
	return 0;
}


int inter_storage_parse_frommap(int fd)
{
	RFIFOHEAD(fd);
	switch(RFIFOW(fd,0)){
	case 0x3016: mapif_parse_LoadMemberStorage(fd); break;
	case 0x3017: mapif_parse_SaveMemberStorage(fd); break;
	case 0x3018: mapif_parse_LoadGuildStorage(fd); break;
	case 0x3019: mapif_parse_SaveGuildStorage(fd); break;
	default:
		return 0;
	}
	return 1;
}
#endif //TXT_SQL_CONVERT
