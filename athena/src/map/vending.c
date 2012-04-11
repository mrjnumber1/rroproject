// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#include "../common/nullpo.h"
#include "../common/showmsg.h"
#include "../common/strlib.h"
#include "../common/utils.h"
#include "clif.h"
#include "itemdb.h"
#include "atcommand.h"
#include "map.h"
#include "path.h"
#include "chrif.h"
#include "vending.h"
#include "irc.h"
#include "pc.h"
#include "skill.h"
#include "battle.h"
#include "log.h"
#include "npc.h"
#include "intif.h"

#include <stdio.h>
#include <string.h>

static int vending_nextid = 1;
struct s_vend_emblem vend_emblems[VEND_CURRENCY_MAX];
/// Returns an unique vending shop id.
static int vending_getuid(void)
{
	return vending_nextid++;
}

void vending_build_emblem_data()
{
	int i;

	memset(&vend_emblems, 0, sizeof(vend_emblems));

	for( i = 0; i <= VEND_CURRENCY_MAX; i++ )
	{ // Emblem Data - Guild ID's
		FILE* fp = NULL;
		char path[256];
		vend_emblems[i].fake_guild_id = SHRT_MAX - 13 - i;
		vend_emblems[i].emblem_id = 1; // Emblem Index


		sprintf(path, "%s/emblems/vend_%d.ebm", db_path, i);
		if( (fp = fopen(path, "rb")) != NULL )
		{
			fseek(fp, 0, SEEK_END);
			vend_emblems[i].emblem_len = ftell(fp);
			fseek(fp, 0, SEEK_SET);
			fread(&vend_emblems[i].emblem_data, 1, vend_emblems[i].emblem_len, fp);
			fclose(fp);
			ShowStatus("Done reading '"CL_WHITE"%s"CL_RESET"' emblem data file.\r", path);
		}
	}
	ShowStatus("Done reading '"CL_WHITE"%d"CL_RESET"' vend emblem data files.\n", i);

}
/*==========================================
 * Close shop
 *------------------------------------------*/
void vending_closevending(struct map_session_data* sd)
{
	nullpo_retv(sd);

	if( sd->state.vending )
	{
		sd->vend_type.currency = VEND_CURRENCY_ZENY;
		sd->vend_type.itemid = 0;

		sd->state.vending = false;
		clif_closevendingboard(&sd->bl, 0);

		if(irc.enabled && irc.shop_flag)
			irc_announce_shop(sd, 0);
	}
}

/*==========================================
 * Request a shop's item list
 *------------------------------------------*/
void vending_vendinglistreq(struct map_session_data* sd, int id)
{
	struct map_session_data* vsd;
	nullpo_retv(sd);

	if( (vsd = map_id2sd(id)) == NULL )
		return;
	if( !vsd->state.vending )
		return; // not vending

	if ( !pc_can_give_items(pc_isGM(sd)) || !pc_can_give_items(pc_isGM(vsd)) ) //check if both GMs are allowed to trade
	{	// GM is not allowed to trade
		clif_displaymessage(sd->fd, msg_txt(246));
		return;
	} 

	sd->vended_id = vsd->vender_id;  // register vending uid

	clif_vendinglist(sd, id, vsd->vending);
}

/*==========================================
 * Purchase item(s) from a shop
 *------------------------------------------*/
void vending_purchasereq(struct map_session_data* sd, int aid, int uid, const uint8* data, int count)
{
	int i, j, k, cursor, w, new_ = 0, blank, vend_list[MAX_VENDING];
	double z, price_buffer=0;
	struct s_vending vending[MAX_VENDING]; // against duplicate packets
	int buyer_currency, seller_currency;
	int limit = MAX_ZENY;
	struct map_session_data* vsd = map_id2sd(aid);

	nullpo_retv(sd);

	
	if (sd->state.npc_vending == uid)
	{ //script vend
		struct npc_data *nd = map_id2nd(uid);

		if (!nd)
			return;

		vending_purchasereq_script(sd, nd, uid, data, count);
		return;
	}

	if( vsd == NULL || !vsd->state.vending || vsd->bl.id == sd->bl.id )
		return; // invalid shop


	if( vsd->vender_id != uid )
	{// shop has changed
		clif_buyvending(sd, 0, 0, 6);  // store information was incorrect
		return;
	}

	if( !searchstore_queryremote(sd, aid) && ( sd->bl.m != vsd->bl.m || !check_distance_bl(&sd->bl, &vsd->bl, AREA_SIZE) ) )
		return; // shop too far away

	searchstore_clearremote(sd);

	if( count < 1 || count > MAX_VENDING || count > vsd->vend_num )
		return; // invalid amount of purchased items

	blank = pc_inventoryblank(sd); //number of free cells in the buyer's inventory

	// duplicate item in vending to check hacker with multiple packets
	memcpy(&vending, &vsd->vending, sizeof(vsd->vending)); // copy vending list

	// some checks
	z = 0.; // zeny counter
	w = 0;  // weight counter
	for( i = 0; i < count; i++ )
	{
		short amount = *(uint16*)(data + 4*i + 0);
		short idx    = *(uint16*)(data + 4*i + 2);
		idx -= 2;

		if( amount <= 0 )
			return;

		// check of item index in the cart
		if( idx < 0 || idx >= MAX_CART )
			return;

		ARR_FIND( 0, vsd->vend_num, j, vsd->vending[j].index == idx );
		if( j == vsd->vend_num )
			return; //picked non-existing item
		else
			vend_list[i] = j;

		z += ((double)vsd->vending[j].value * (double)amount);

		switch(vsd->vend_type.currency)
		{
		case VEND_CURRENCY_ZENY:
			buyer_currency = sd->status.zeny;
			seller_currency = vsd->status.zeny;
			break;
		case VEND_CURRENCY_REG:
			{
				char var[32];
				int type = vsd->vend_type.reg_type;

				strncpy(var, vsd->vend_type.reg, 32);

				buyer_currency = pc_readregistry(sd, var, type);
				seller_currency = pc_readregistry(vsd, var, type);
			}
			break;
		case VEND_CURRENCY_KAFRA:
			buyer_currency = pc_readaccountreg2(sd, "##KAFRAPOINTS");
			seller_currency = pc_readaccountreg2(vsd, "##KAFRAPOINTS");
			break;
		case VEND_CURRENCY_ITEM:
			k = pc_search_inventory(sd,vsd->vend_type.itemid);

			if(k >= 0)
				buyer_currency = sd->status.inventory[k].amount;
			else 
				buyer_currency = 0;

			k = pc_search_inventory(vsd,vsd->vend_type.itemid);
			if(k >= 0)
				seller_currency = vsd->status.inventory[k].amount;
			else 
				seller_currency = 0;

			limit = MAX_AMOUNT;
			break;
		default:
			ShowError("vending_vendcurrency: vend_type.currency was impossible to find! (buyer cid: %d | seller cid: %d)", sd->status.char_id, vsd->status.char_id);
			buyer_currency = sd->status.zeny;
			seller_currency = vsd->status.zeny;
			break;
		}

		if( z > (double)buyer_currency || z < 0. || z > (double)limit )	
		{
			clif_buyvending(sd, idx, amount, 1); // you don't have enough zeny
			return;
		}
		if( z + (double)seller_currency > (double)limit && !battle_config.vending_over_max )
		{
			clif_buyvending(sd, idx, vsd->vending[j].amount, 4); // too much zeny = overflow
			return;
		}
		w += itemdb_weight(vsd->status.cart[idx].nameid) * amount;
		if( w + sd->weight > sd->max_weight )
		{
			clif_buyvending(sd, idx, amount, 2); // you can not buy, because of weight
			return;
		}
		
		//Check to see if cart/vend info is in sync.
		if( vending[j].amount > vsd->status.cart[idx].amount )
			vending[j].amount = vsd->status.cart[idx].amount;
		
		// if they try to add packets (example: get twice or more 2 apples if marchand has only 3 apples).
		// here, we check cumulative amounts
		if( vending[j].amount < amount )
		{
			// send more quantity is not a hack (an other player can have buy items just before)
			clif_buyvending(sd, idx, vsd->vending[j].amount, 4); // not enough quantity
			return;
		}
		
		vending[j].amount -= amount;

		switch( pc_checkadditem(sd, vsd->status.cart[idx].nameid, amount) ) 
		{
		case ADDITEM_EXIST:
			break;	//We'd add this item to the existing one (in buyers inventory)
		case ADDITEM_NEW:
			new_++;
			if (new_ > blank)
				return; //Buyer has no space in his inventory
			break;
		case ADDITEM_OVERAMOUNT:
			return; //too many items
		}
	}

	if(vsd->vend_type.currency != VEND_CURRENCY_ZENY)
	{
		
		int flag, get_count;
		struct item it;

		switch(vsd->vend_type.currency)
		{
		case VEND_CURRENCY_ITEM:
			k = pc_search_inventory(sd, vsd->vend_type.itemid);
			it.nameid = vsd->vend_type.itemid;

			if(k>=0)
				pc_delitem(sd,k,(int)z,0,0);
			else
				return;

			if (!itemdb_isstackable(vsd->vend_type.itemid))
				get_count = 1;
			else
				get_count = (int)z;

			for (i = 0; i < (int)z; i += get_count)
			{
				if ((flag = pc_additem(vsd, &it, get_count)))
					clif_additem(sd, 0, 0, flag);

				it.serial = 0; // Reset Serial to generate new
			}

			log_pick_pc(vsd, LOG_TYPE_VENDING, vsd->vend_type.itemid, (int)z, &it, it.serial);
			log_pick_pc( sd, LOG_TYPE_VENDING, vsd->vend_type.itemid, -(int)z, &it, it.serial);

			break;
		case VEND_CURRENCY_REG:
			{
				char var[32];
				int type = vsd->vend_type.reg_type;

				strncpy(var, vsd->vend_type.reg, 32);

				pc_setregistry( sd, var, pc_readregistry( sd, var, type) - (int)z, type);
				pc_setregistry(vsd, var, pc_readregistry(vsd, var, type) + (int)z, type);
			}
			break;
		case VEND_CURRENCY_KAFRA:
			pc_paycash(sd, (int)z, (int)z);
			pc_getcash(vsd, 0, (int)z);

			break;

		}
	}
	else
	{
		//Logs (V)ending Zeny [Lupus]
		log_zeny(vsd, LOG_TYPE_VENDING, sd, (int)z);

		pc_payzeny(sd, (int)z);
		if( battle_config.vending_tax )
			z -= z * (battle_config.vending_tax/10000.);
		pc_getzeny(vsd, (int)z);
	}

	for( i = 0; i < count; i++ )
	{
		short amount = *(uint16*)(data + 4*i + 0);
		short idx    = *(uint16*)(data + 4*i + 2);
		idx -= 2;

		//Logs sold (V)ending items [Lupus]
		log_pick_pc(vsd, LOG_TYPE_VENDING, vsd->status.cart[idx].nameid, -amount, &vsd->status.cart[idx], vsd->status.cart[idx].serial);
		log_pick_pc( sd, LOG_TYPE_VENDING, vsd->status.cart[idx].nameid,  amount, &vsd->status.cart[idx], vsd->status.cart[idx].serial);

		// vending item
		pc_additem(sd, &vsd->status.cart[idx], amount);
		vsd->vending[vend_list[i]].amount -= amount;

		//send notice to autotrader's other online chars (in same member id) or mail the autotrader details if no one received the message
		if(vsd->state.autotrade) 
		{
			struct s_mapiterator* iter;
			struct map_session_data* pl_sd;
			int messaged = 0;
			char body[MAIL_BODY_LENGTH];

			snprintf(body, MAIL_BODY_LENGTH, "%s bought %s (%d ea)", sd->status.name, itemdb_jname(vsd->status.cart[idx].nameid), amount);

			iter = mapit_geteachpc();
			for( pl_sd = (struct map_session_data*)mapit_first(iter); mapit_exists(iter); pl_sd = (struct map_session_data*)mapit_next(iter) )
				if( pl_sd != sd && pl_sd->status.member_id == vsd->status.member_id)
				{
					messaged = 1;
					clif_disp_onlyself(vsd, body, strlen(body));
				}
			mapit_free(iter);

			if (!messaged)
			{
				struct mail_message *msg = NULL;

				msg->id = 0;
				msg->send_id = 0;
				safestrncpy(msg->send_name, "Server", NAME_LENGTH);
				msg->dest_id = vsd->status.char_id;
				safestrncpy(msg->dest_name, vsd->status.name, NAME_LENGTH);
				safestrncpy(msg->title, "Autotrade System", MAIL_TITLE_LENGTH);
				safestrncpy(msg->body, body, MAIL_BODY_LENGTH);
				msg->status = MAIL_NEW;
				msg->zeny = 0;
				msg->timestamp = time(NULL);
				memset(&msg->item, 0, sizeof(struct item));
				
				intif_Mail_send(0, msg);
			}

		}

		pc_cart_delitem(vsd, idx, amount, 0);
		clif_vendingreport(vsd, idx, amount);

		//print buyer's name
		if( battle_config.buyer_name )
		{
			char temp[CHAT_SIZE_MAX];
			sprintf(temp, msg_txt(265), sd->status.name);
			clif_disp_onlyself(vsd,temp,strlen(temp));
		}
	}

	// compact the vending list
	for( i = 0, cursor = 0; i < vsd->vend_num; i++ )
	{
		if( vsd->vending[i].amount == 0 )
			continue;
		
		if( cursor != i ) // speedup
		{
			vsd->vending[cursor].index = vsd->vending[i].index;
			vsd->vending[cursor].amount = vsd->vending[i].amount;
			vsd->vending[cursor].value = vsd->vending[i].value;
		}

		cursor++;
	}
	vsd->vend_num = cursor;

	//Always save BOTH: buyer and customer
	if( save_settings&2 )
	{
		chrif_save(sd,0);
		chrif_save(vsd,0);
	}

	//check for @AUTOTRADE users [durf]
	if( vsd->state.autotrade )
	{
		//see if there is anything left in the shop
		ARR_FIND( 0, vsd->vend_num, i, vsd->vending[i].amount > 0 );
		if( i == vsd->vend_num )
		{
			//Close Vending (this was automatically done by the client, we have to do it manually for autovenders) [Skotlex]
			vending_closevending(vsd);
			map_quit(vsd);	//They have no reason to stay around anymore, do they?
		}
	}
}




void vending_purchasereq_script(struct map_session_data* sd, struct npc_data* nd, int uid, const uint8* data, int count)
{
	int i, k;
	int add;
	int blank;
	int weight;
	double zeny;
	int buyer_currency, seller_currency;
	int limit = MAX_ZENY;

	nullpo_retv(sd);
	nullpo_retv(nd);
	
	if( sd->state.npc_vending != nd->bl.id || uid != nd->bl.id )
	{
		clif_buyvending(sd, 0, 0, 6);
		return;
	}

	if( count < 1 || count > MAX_VENDING )
		return;

	blank = pc_inventoryblank(sd);

	add = 0;
	zeny = 0;
	weight = 0;

	for( i = 0; count > i; i++ )
	{
		short amount = *(uint16*)(data + 4*i + 0);
		short index  = *(uint16*)(data + 4*i + 2);
		struct item_data* data;

		if( amount <= 0 )
			continue;

		index -= 2;

		if( index < 0 || index >= MAX_VENDING || nd->vending[index].nameid == 0 )
			continue;
		
		data = itemdb_exists(nd->vending[index].nameid);

		if( data == NULL )
			continue;

		zeny += (double)( nd->vending[index].value * amount );

		switch(nd->vend_type.currency)
		{
			case VEND_CURRENCY_ZENY:
				buyer_currency = sd->status.zeny;
				seller_currency = 0;
				break;
			case VEND_CURRENCY_REG:
				{
					char var[32];
					int type = nd->vend_type.reg_type;

					strncpy(var, nd->vend_type.reg, 32);

					buyer_currency = pc_readregistry(sd, var, type);
					seller_currency = 0;
				}
				break;
			case VEND_CURRENCY_KAFRA:
				buyer_currency = pc_readaccountreg2(sd, "##KAFRAPOINTS");
				seller_currency = 0;
				break;
			case VEND_CURRENCY_ITEM:
				k = pc_search_inventory(sd,nd->vend_type.itemid);

				if(k >= 0)
					buyer_currency = sd->status.inventory[k].amount;
				else 
					buyer_currency = 0;

				seller_currency = 0;

				limit = MAX_AMOUNT;
				break;
			default:
				ShowError("vending_vendcurrency_script: vend_type.currency was impossible to find! (buyer cid: %d | seller uid: %d)", sd->status.char_id, nd->bl.id);
				buyer_currency = sd->status.zeny;
				seller_currency = 0;
				break;
		}

		if( zeny > (double)buyer_currency || zeny < 0.0 || zeny > (double)limit )
		{
			clif_buyvending(sd, index, amount, 1);
			return;
		}

		weight += data->weight * amount;
		if ( buyer_currency && (nd->vend_type.currency = VEND_CURRENCY_ITEM) )
		{
			k = pc_search_inventory(sd, nd->vend_type.itemid);

			if (k > 0)
			{
				weight -= itemdb_weight(sd->status.inventory[k].nameid) * amount;
			}

		}

		if( weight + sd->weight > sd->max_weight )
		{
			clif_buyvending(sd, index, amount, 2);
			return;
		}

		switch( pc_checkadditem(sd, nd->vending[index].nameid, amount) )
		{
			case ADDITEM_OVERAMOUNT:
				return;
			case ADDITEM_NEW:
				add++;
		}
	}

	if( add > blank )
		return;

	if (nd->vend_type.currency != VEND_CURRENCY_ZENY)
	{
		struct item it;
		memset(&it, 0, sizeof(it));

		switch(nd->vend_type.currency)
		{
		case VEND_CURRENCY_ITEM:
			k = pc_search_inventory(sd, nd->vend_type.itemid);
			it.nameid = nd->vend_type.itemid;

			if(k>=0)
				pc_delitem(sd,k,(int)zeny,0,0);
			else
				return;


			//log_pick_pc(vsd, LOG_TYPE_VENDING, vsd->vend_type.itemid, (int)z, &it, it.serial);
			log_pick_pc( sd, LOG_TYPE_SCRIPT, nd->vend_type.itemid, -(int)zeny, &it, it.serial);

			break;
		case VEND_CURRENCY_REG:
			{
				char var[32];
				int type = nd->vend_type.reg_type;

				strncpy(var, nd->vend_type.reg, 32);

				pc_setregistry( sd, var, pc_readregistry( sd, var, type) - (int)zeny, type);
				//pc_setregistry(vsd, var, pc_readregistry(vsd, var, type) + (int)z, type);
			}
			break;
		case VEND_CURRENCY_KAFRA:
			pc_paycash(sd, (int)zeny, (int)zeny);
			//pc_getcash(vsd, 0, (int)z);

			break;

		}
	}
	else
	{
		pc_payzeny(sd, (int)zeny);

		// TODO: um, how do we log this?

		//log_zeny(sd, LOG_TYPE_SCRIPT, NULL, (int)-z);
		//log_pick_pc(sd, LOG_TYPE_NPC, nameid, amount, NULL, item_tmp.serial);

		pc_payzeny(sd, (int)zeny);
	}
	for( i = 0; count > i; i++ )
	{
		short amount = *(uint16*)(data + 4*i + 0);
		short index  = *(uint16*)(data + 4*i + 2);
		int flag = 0;
		struct item add_item;
		
		index -= 2;

		memset( &add_item, 0, sizeof(struct item) );

		add_item.nameid = nd->vending[index].nameid;
		add_item.refine = nd->vending[index].refine;
		add_item.identify = 1;
		add_item.attribute = nd->vending[index].attribute;
		
		memcpy( add_item.card, nd->vending[index].card, sizeof(nd->vending[index].card) );

		if(nd->vending[index].rent_time)
		{
			add_item.expire_time = (unsigned int)(time(NULL) + nd->vending[index].rent_time);
		}
		
		if( (flag = pc_additem(sd, &add_item, 1)) )
		{
			clif_additem(sd, 0, 0, flag);
			return;
		}

		if(nd->vending[index].rent_time)
		{
			//clif_rental_time(sd->fd, add_item.nameid, add_item.expire_time);
			pc_inventory_rental_add(sd, add_item.expire_time);
		}

		log_pick_pc(sd, LOG_TYPE_SCRIPT, add_item.nameid, 1, &add_item, add_item.serial);
		
	}

	if( save_settings&2 )
		chrif_save(sd, 0);
}



/*==========================================
 * Open shop
 * data := {<index>.w <amount>.w <value>.l}[count]
 *------------------------------------------*/
void vending_openvending(struct map_session_data* sd, const char* message, bool flag, const uint8* data, int count)
{
	int i, j, char_id;
	int vending_skill_lvl;
	nullpo_retv(sd);

	if( !flag ) // cancelled
		return; // nothing to do

	if (pc_isdead(sd) || !sd->state.prevend || pc_istrading(sd))
		return; // dead or wpe use or can't have 2 shops at once

	vending_skill_lvl = pc_checkskill(sd, MC_VENDING);
	// skill level and cart check
	if( !vending_skill_lvl || !pc_iscarton(sd) )
	{
		clif_skill_fail(sd, MC_VENDING, USESKILL_FAIL_LEVEL, 0);
		return;
	}

	// check number of items in shop
	if( count < 1 || count > MAX_VENDING || count > 2 + vending_skill_lvl )
	{	// invalid item count
		clif_skill_fail(sd, MC_VENDING, USESKILL_FAIL_LEVEL, 0);
		return;
	}

	// filter out invalid items
	i = 0;
	for( j = 0; j < count; j++ )
	{
		short index        = *(uint16*)(data + 8*j + 0);
		short amount       = *(uint16*)(data + 8*j + 2);
		unsigned int value = *(uint32*)(data + 8*j + 4);

		index -= 2; // offset adjustment (client says that the first cart position is 2)

		if( index < 0 || index >= MAX_CART // invalid position
		||  pc_cartitem_amount(sd, index, amount) < 0 // invalid item or insufficient quantity
		//NOTE: official server does not do any of the following checks!
		||  !sd->status.cart[index].identify // unidentified item
		||  sd->status.cart[index].attribute == 1 // broken item
		||  sd->status.cart[index].expire_time // It should not be in the cart but just in case
		||  !itemdb_cantrade(&sd->status.cart[index], pc_isGM(sd), pc_isGM(sd)) // untradeable item
		||  ( sd->status.cart[index].card[0] == CARD0_CREATE 
			&& (char_id = MakeDWord(sd->status.cart[index].card[2],sd->status.cart[index].card[3])) > 0 
			&& ( (BG_CHARID && char_id == BG_CHARID && BG_TRADE&2) 
				|| (WOE_CHARID && char_id == WOE_CHARID && WOE_TRADE&2)
				|| (RAID_CHARID && char_id == RAID_CHARID && RAID_TRADE&2)
				) 
			)
 ) // 
			continue;

		sd->vending[i].index = index;
		sd->vending[i].amount = amount;
		sd->vending[i].value = cap_value(value, 0, (unsigned int)battle_config.vending_max_value);

		i++; // item successfully added
	}

	if( i != j )
		clif_displaymessage (sd->fd, msg_txt(266)); //"Some of your items cannot be vended and were removed from the shop."

	if( i == 0 )
	{	// no valid item found
		clif_skill_fail(sd, MC_VENDING, USESKILL_FAIL_LEVEL, 0); // custom reply packet
		return;
	}

	sd->state.vending = true;
	sd->state.prevend = 0;
	sd->vender_id = vending_getuid();
	sd->vend_num = i;
	safestrncpy(sd->message, message, MESSAGE_SIZE);

	pc_stop_walking(sd,1);
	clif_openvending(sd,sd->bl.id,sd->vending);
	clif_showvendingboard(&sd->bl,message,0);
	clif_vend_emblem(sd,sd->vend_type.currency);

	if(irc.enabled && irc.shop_flag)
		irc_announce_shop(sd, 1);
}


/// Checks if an item is being sold in given player's vending.
bool vending_search(struct map_session_data* sd, unsigned short nameid)
{
	int i;

	if( !sd->state.vending )
	{// not vending
		return false;
	}

	ARR_FIND( 0, sd->vend_num, i, sd->status.cart[sd->vending[i].index].nameid == (short)nameid );
	if( i == sd->vend_num )
	{// not found
		return false;
	}

	return true;
}


/// Searches for all items in a vending, that match given ids, price and possible cards.
/// @return Whether or not the search should be continued.
bool vending_searchall(struct map_session_data* sd, const struct s_search_store_search* s)
{
	int i, c, slot;
	unsigned int idx, cidx;
	struct item* it;

	if( !sd->state.vending )
	{// not vending
		return true;
	}

	for( idx = 0; idx < s->item_count; idx++ )
	{
		ARR_FIND( 0, sd->vend_num, i, sd->status.cart[sd->vending[i].index].nameid == (short)s->itemlist[idx] );
		if( i == sd->vend_num )
		{// not found
			continue;
		}
		it = &sd->status.cart[sd->vending[i].index];

		if( s->min_price && s->min_price > sd->vending[i].value )
		{// too low price
			continue;
		}

		if( s->max_price && s->max_price < sd->vending[i].value )
		{// too high price
			continue;
		}

		if( s->card_count )
		{// check cards
			if( itemdb_isspecial(it->card[0]) )
			{// something, that is not a carded
				continue;
			}
			slot = itemdb_slot(it->nameid);

			for( c = 0; c < slot && it->card[c]; c ++ )
			{
				ARR_FIND( 0, s->card_count, cidx, s->cardlist[cidx] == it->card[c] );
				if( cidx != s->card_count )
				{// found
					break;
				}
			}

			if( c == slot || !it->card[c] )
			{// no card match
				continue;
			}
		}

		if( !searchstore_result(s->search_sd, sd->vender_id, sd->status.account_id, sd->message, it->nameid, sd->vending[i].amount, sd->vending[i].value, it->card, it->refine) )
		{// result set full
			return false;
		}
	}

	return true;
}


void do_init_vending()
{
	vending_build_emblem_data();

}



