// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#include "../common/core.h"
#include "../common/socket.h"
#include "../common/malloc.h"
#include "../common/db.h"
#include "../common/timer.h"
#include "../common/strlib.h"
#include "../common/mmo.h"
#include "../common/showmsg.h"
#include "../common/nullpo.h"

#include "map.h"
#include "mob.h"
#include "pc.h"
#include "intif.h" //For GM Broadcast
#include "irc.h"

#include <stdio.h>
#include <string.h>
#include <stdlib.h>

char irc_channel[32] = "";
char irc_channel_pass[32] = "";
char irc_ip_str[128] = "";
char irc_nick[30] = "";
char irc_password[32] = "";

unsigned long  irc_ip = 0;
unsigned short irc_port = 6667;

// configuration
struct e_irc_config irc;

// variables
int irc_fd = 0;
int last_cd_user = 0;
struct e_irc_session* irc_session_info = NULL;
struct e_channel_data cd;

//======================================================
// Read Config File (irc_athena.conf)
//======================================================
int irc_read_conf(char *file)
{
	FILE* fp;
	char w1[256], w2[256];
	char path[256];
	char row[1024];

	memset( w1, '\0', sizeof(w1) );
	memset( w2, '\0', sizeof(w2) );
	memset( path, '\0', sizeof(path) );
	memset( row, '\0', sizeof(row) );

	snprintf( path, sizeof(path), "conf/%s", file );

	if( !(fp = fopen(path,"r")) )
	{// failed to open the configuration file
		ShowError( "Cannot find file: %s\n", path );
		return 0;
	}

	while( fgets(row, sizeof(row), fp) )
	{
		if( row[0] == '/' && row[1] == '/' )
			continue;

		sscanf( row, "%[^:]: %255[^\r\n]", w1, w2 );

		if(strcmpi(w1,"irc.enabled")==0)
			irc.enabled = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc_server")==0)
			safestrncpy(irc_ip_str,w2,sizeof(irc_ip_str));
		else if(strcmpi(w1,"irc_port")==0)
			irc_port = atoi(w2);
		else if(strcmpi(w1,"irc.autojoin")==0)
			irc.autojoin = (unsigned char)atoi(w2);
		else if(strcmpi(w1,"irc_channel")==0)
			safestrncpy(irc_channel,w2,sizeof(irc_channel));
		else if(strcmpi(w1,"irc_channel_pass")==0)
			safestrncpy(irc_channel_pass,w2,sizeof(irc_channel_pass));
		else if(strcmpi(w1,"irc_nick")==0)
			safestrncpy(irc_nick,w2,sizeof(irc_nick));
		else if(strcmpi(w1,"irc_pass")==0) {
			if(strcmpi(w2,"0")!=0)
				safestrncpy(irc_password,w2,sizeof(irc_password));
		}
		else if(strcmpi(w1,"irc.announce_flag")==0)
			irc.announce_flag = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc.job_change_flag")==0)
			irc.job_change_flag = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc.main_auto")==0)
			irc.main_auto = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc.main_flag")==0)
			irc.main_flag = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc.mvp_flag")==0)
			irc.mvp_flag = (unsigned char)config_switch(w2);
		else if(strcmpi(w1,"irc.shop_flag")==0)
			irc.shop_flag = (unsigned char)config_switch(w2);
	}

	ShowInfo("IRC configuration has been read successfully.\n");
	fclose(fp);
	return 1;
}


int irc_connect_timer(int tid, unsigned int tick, int id, intptr data)
{
	if(irc_session_info && session[irc_session_info->fd])
		return 0;
	//Ok, this ShowInfo and printf are a little ugly, but they are meant to
	//debug just how long the code freezes here. [Skotlex]
	ShowInfo("(IRC) Connecting to %s... \n", irc_ip_str);
	irc_fd = make_connection(irc_ip,irc_port);
	if(irc_fd > 0){
		ShowMessage("ok\n");
		session[irc_fd]->func_parse = irc_parse;
	} else
		ShowMessage("failed\n");
	return 0;
}

//======================================================
// Displays announcements in IRC
//======================================================
void irc_announce(const char* buf)
{
	char send_string[MAX_SEND_LENGTH];

	// print the message into the buffer and send to the IRC server
	snprintf( send_string, sizeof(send_string), "PRIVMSG %s :<BROADCAST> %s", irc_channel, buf );
	irc_send( send_string );
}

//======================================================
// Displays main chat in IRC
//======================================================
void irc_announce_main(const char* buf)
{
	char send_string[MAX_SEND_LENGTH];

	// print the message into the buffer and send to the IRC server
	snprintf( send_string, sizeof(send_string), "PRIVMSG %s : %s", irc_channel, buf );
	irc_send( send_string );
}


//======================================================
// Displays Job Changes in IRC
// Note: only shows job changes from npc jobchange
//		 function.
//======================================================
void irc_announce_jobchange(struct map_session_data *sd)
{
	char send_string[256];

	// ensure the parses session_data is valid
	nullpo_retv( sd );

	// print the job change message into the output buffer and send to the IRC server
	snprintf( send_string, sizeof(send_string), "PRIVMSG %s :%s has changed into a %s.", irc_channel, sd->status.name, job_name(sd->status.class_) );
	irc_send( send_string );
}

//======================================================
// Displays shop open/closing in IRC.
//======================================================
void irc_announce_shop(struct map_session_data *sd, int flag)
{
	char send_string[MAX_SEND_LENGTH];
	nullpo_retv(sd);

	// TODO: we need to make it link the users a page with the shop details
	if( flag ) // player opened a shop
		snprintf( send_string, sizeof(send_string), "PRIVMSG %s :%s has opened the shop [%s]", irc_channel, sd->status.name, sd->message);
	//else 
	//	snprintf( send_string, sizeof(send_string), "PRIVMSG %s :%s has closed %s shop.", irc_channel, sd->status.name, sd->status.sex ? "his" : "her" );

	// send the message to the IRC server
	irc_send(send_string);
}

//======================================================
// Displays MVP kills in IRC
//======================================================
void irc_announce_mvp(struct map_session_data *sd, struct mob_data *md)
{
	char send_string[MAX_SEND_LENGTH];

	// ensure both session_data and mob_data both exist before attempting to access them
	nullpo_retv(sd);
	nullpo_retv(md);

	// print the mvp kill message into the output buffer and send to the IRC server
	snprintf( send_string, sizeof(send_string), "PRIVMSG %s :%s (%s) has MVP'd %s!", irc_channel, sd->status.name, job_name(sd->status.class_), md->name);
	irc_send( send_string );
}

//======================================================
// Parses content in IRC channel
//======================================================
int irc_parse(int fd)
{
	if( session[fd]->flag.eof )
	{// closing IRC session so release any of the allocated memory
		do_close( fd );
		irc_session_info = NULL;
		add_timer(gettick() + 15000, irc_connect_timer, 0, 0);
      	return 0;
	}

	if( session[fd]->session_data == NULL )
	{// ensure that the session information is constructed
		irc_session_info = (struct e_irc_session*)aMalloc(sizeof(struct e_irc_session));
		irc_session_info->fd = fd;
		irc_session_info->state = 0;
		session[fd]->session_data = irc_session_info;
	}

	if( !irc_session_info )
	{// copy the same session information from the socket
		irc_session_info = (struct e_irc_session*)session[fd]->session_data;
		irc_session_info->fd = fd;
	}

	if( RFIFOREST(fd) > 0 )
	{// remaining packets exist so process the packet and continue
		send_to_parser( fd, (char*)RFIFOP(fd,0), "\n" );
		RFIFOSKIP( fd, RFIFOREST(fd) );
	}

	return 0;
}
//======================================================
// Sends keepalive when necessary
//======================================================
int irc_keepalive_timer(int tid, unsigned int tick, int id, intptr data)
{
	char send_string[MAX_SEND_LENGTH];

	// send a singular packet with no data to ensure that the connection lives
	snprintf(send_string,sizeof(send_string),"PRIVMSG %s : ", irc_nick);
	irc_send(send_string);

	// ensure that this function is called again to ensure survival
	add_timer(gettick() + 30000, irc_keepalive_timer, 0, 0);

	return 0;
}

//======================================================
// Function to send message to IRC channel
//======================================================
void irc_send(char *buf)
{
	int len;
	int fd;

	if( !irc_session_info || !(fd = irc_session_info->fd) || !session[fd] )
	{// no connection available or socket non-existant
		return;
	}

	len = strlen(buf) + 1;
	WFIFOHEAD(fd, len);
	sprintf((char*)WFIFOP(fd,0), "%s\n", buf);
	WFIFOSET(fd, len);
}

//======================================================
// Parses content in IRC channel
//======================================================
void irc_parse_sub(int fd, char *incoming_string)
{
	char source[256];
	char command[256];
	char target[256];
	char message[1024];
	char send_string[1024];
	char ping_message[256];
	char ssource[256];
	char *source_nick=NULL;
	char *source_ident=NULL;
	char *source_host=NULL;
	char *state_mgr=NULL;

	memset( source, 0, sizeof(source) );
	memset( command, 0, sizeof(command) );
	memset( target, 0, sizeof(target) );
	memset( message, 0, sizeof(message) );
	memset( send_string, 0, sizeof(send_string) );

	sscanf( incoming_string, ":%255s %255s %255s :%1023[^\r\n]", source, command, target, message );
	sscanf( incoming_string, "%255s %255s[^\r\n]", ssource, ping_message );

	if( source != NULL )
	{// received source name from the packet so store the credentials
		if( strchr(source, '!') ) {
			source_nick =  strtok_r( source, "!", &state_mgr );
			source_ident = strtok_r( NULL,   "@", &state_mgr );
			source_host =  strtok_r( NULL,   "%%",&state_mgr );
		}
	}

	switch( irc_session_info->state )
	{
		case 0:
		{// not yet identified so send identification credentials
			snprintf(send_string, sizeof(send_string), "USER karmaRObot * :karmaRObot");
			irc_send(send_string);
			snprintf(send_string, sizeof(send_string), "NICK %s", irc_nick);
			irc_send(send_string);
			irc_session_info->state = 1;
		}
		break;

		case 1:
		{// identified but awaiting confirmation from the IRC server
			if( !strcmp(ssource, "PING") )
			{// reply to a PING message with a PONG message
				snprintf( send_string, sizeof(send_string), "PONG %s", ping_message );
				irc_send( send_string );
			}
			else if( !strcmp(command,"001") )
			{// received confirmation from the IRC server that the connection was successful.
				ShowStatus("IRC: Connected to IRC.\n");

				if( irc_password[0] != '\0' )
				{// send password identification associated with this nickname
					snprintf( send_string, sizeof(send_string), "PRIVMSG nickserv :identify %s", irc_password );
					irc_send( send_string );
				}

				// join the appropriate channels and retrieve a list of all participants
				snprintf( send_string, sizeof(send_string), "JOIN %s %s", irc_channel, irc_channel_pass );
				irc_send( send_string );
				snprintf( send_string, sizeof(send_string), "NAMES %s",irc_channel );
				irc_send( send_string );
				irc_session_info->state = 2;
			}
			else if( !strcmp(command,"433") )
			{// nickname is already in use so enforce an error and cancel
				ShowError( "IRC: Nickname %s is already taken, IRC Client unable to connect.\n", irc_nick );
				snprintf( send_string, sizeof(send_string), "QUIT" );
				irc_send( send_string );
				if( session[fd] )
					set_eof( fd );
			}
		}
		break;

		case 2:
		{// identified and connected to the IRC server successfully so process other messages
			if( !strcmp(source, "PING") )
			{// reply to a PING message with a PONG message
				snprintf( send_string, sizeof(send_string), "PONG %s", command );
				irc_send( send_string );
			}
			else if ( strcmpi(target,irc_channel) == 0 || strcmpi( target, irc_channel + 1 ) == 0 || strcmpi( target + 1, irc_channel ) == 0 )
			{// message was received from the channel exclusively
				char cmdname[256];
				char cmdargs[256] = "";

				if( !strcmpi(command,"privmsg") && target[0] == '#')
				{// private message (public really) from the channel
					if( irc.main_auto )
					{// messages spoken in the IRC channel should be relayed as @main messages
						snprintf( send_string, sizeof(send_string), ">> (IRC)%s : %s", source_nick, message );
						intif_broadcast2( send_string, strlen(send_string) + 1, 0xFE000000, 0, 0, 0, 0 );
					}

					if( sscanf(message, "@%255s %255[^\r\n]", cmdname, cmdargs) )
					{// at-command used in the IRC channel so perform any commands here
						if( !strcmpi(cmdname, "users") || !strcmpi(cmdname, "who") )
						{// @users, @who
							snprintf( send_string, sizeof(send_string), "PRIVMSG %s :Users online: %d", irc_channel, map_getusers() );
							irc_send( send_string );
						}
						else if( !strcmpi(cmdname, "main") && !irc.main_auto )
						{// @main
							snprintf( send_string, sizeof(send_string), ">> (IRC)%s : %s", source_nick, cmdargs );
							intif_broadcast2( send_string, strlen(send_string) + 1, 0xFE000000, 0, 0, 0, 0 );
						}
					}
					//else if( strstr(message, "ACTION rubs RaijeBot's tummy") )
					//{// someone use /me rubs RaijeBot's tummy
					//	sprintf( send_string, "PRIVMSG %s :*purr*", irc_channel );
					//	irc_send( send_string );
					//}
				}
				else if ( !strcmpi(command, "join") || !strcmpi(command, "part") || !strcmpi(command, "mode") || !strcmpi(command, "nick") )
				{// retrieve the list of channel names again
					ShowInfo( "IRC: Refreshing user list" );
					irc_rmnames();
					ShowMessage( "..." );
					snprintf( send_string, sizeof(send_string), "NAMES %s", irc_channel );
					irc_send( send_string );
					ShowMessage( "complete.\n" );
				}
				else if( !strcmpi(command, "kick") && irc.autojoin )
				{// auto-join when kicked
					snprintf( send_string, sizeof(send_string), "JOIN %s %s", target, irc_channel_pass );
					irc_send( send_string );
				}
			}
			else if( !strcmpi(command, "353") )
			{// reply to the names packet
				ShowInfo( "IRC: NAMES received\n" );
				parse_names_packet( incoming_string );
			}
		}
		break;
	}
}

int send_to_parser(int fd, char* input, char key[2])
{
	char* temp_string = NULL;
	char* state_mgr = NULL;
	int total_loops = 0;

	// process the line until the next delimiter
	temp_string = strtok_r( input, key, &state_mgr );

	// while a line exists in the buffer
	while( temp_string )
	{
		total_loops++;
		irc_parse_sub( fd, temp_string );
		temp_string = strtok_r( NULL, key, &state_mgr );
	}

	return total_loops;
}

//======================================================
// NAMES Packet(353) parser
//======================================================
int parse_names_packet(char *str)
{
	char* tok;
	char source[256];
	char numeric[10];
	char target[256];
	char channel[256];
	char names[1024];

	memset(source,'\0',256);
	memset(numeric,'\0',10);
	memset(target,'\0',256);
	memset(channel,'\0',256);
	memset(names,'\0',1024);

	//TODO: fold this
	tok=strtok(str,"\r\n");
	sscanf(tok,":%255s %10s %255s %*1[=@] %255s :%1023[^\r\n]",source,numeric,target,channel,names);
	if(strcmpi(numeric,"353")==0)
		parse_names(names);

	while((tok=strtok(NULL,"\r\n"))!=NULL)
	{
		sscanf(tok,":%255s %10s %255s %*1[=@] %255s :%1023[^\r\n]",source,numeric,target,channel,names);
		if(strcmpi(numeric,"353")==0)
			parse_names(names);
	}

	return 0;
}

//======================================================
// User access level prefix parser
//======================================================
int parse_names(char* str)
{
	char* tok;

	if( !str )
	{// no usernames received to parse
		return 0;
	}

	tok = strtok( str, " " );

	switch(tok[0])
	{
		case '~': set_access(tok+1,ACCESS_OWNER); break;
		case '&': set_access(tok+1,ACCESS_SOP); break;
		case '@': set_access(tok+1,ACCESS_OP); break;
		case '%': set_access(tok+1,ACCESS_HOP); break;
		case '+': set_access(tok+1,ACCESS_VOICE); break;
		default : set_access(tok,ACCESS_NORM); break;
	}

	while((tok = strtok(NULL, " ")) != NULL)
	{
		switch(tok[0])
		{
			case '~': set_access(tok+1,ACCESS_OWNER); break;
			case '&': set_access(tok+1,ACCESS_SOP); break;
			case '@': set_access(tok+1,ACCESS_OP); break;
			case '%': set_access(tok+1,ACCESS_HOP); break;
			case '+': set_access(tok+1,ACCESS_VOICE); break;
			default : set_access(tok,ACCESS_NORM); break;
		}
	}

	return 1;
}

//======================================================
// Store user's access level
//======================================================
int set_access(char* nick, int newlevel )
{
	int i;

	// find the entry matching this nickname
	ARR_FIND( 0, MAX_CHANNEL_USERS + 1, i, !strcmpi(cd.user[i].name, nick) );

	if( i < MAX_CHANNEL_USERS + 1 )
	{// update the level of the existing entry
		cd.user[i].level = (unsigned char)newlevel;
		return 1;
	}

	// copy the nickname into the user list for future reference
	safestrncpy( cd.user[last_cd_user].name, nick, sizeof(cd.user[last_cd_user].name) );

	// copy the new level of this nickname
	cd.user[last_cd_user++].level = (unsigned char)newlevel;

	return 0;
}

//======================================================
// Returns users access level
//======================================================
int get_access(char *nick)
{
	int i;

	// find the entry matching this nickname
	ARR_FIND( 0, MAX_CHANNEL_USERS + 1, i, !strcmpi(cd.user[i].name, nick) );

	// push the level of the person or -1 from the method
	return ( i == MAX_CHANNEL_USERS + 1 ) ? -1 : cd.user[i].level;
}

int irc_rmnames()
{
	// reset the entire user block
	memset( cd.user, 0, sizeof(cd.user) );

	// reset the user block counter to nothing
	last_cd_user = 0;

	return 0;
}

void do_init_irc(void)
{
	if( !irc.enabled )
	{// do not process the IRC server at all
		return;
	}

	// retrieve the long IP from the string value
	irc_ip = host2ip(irc_ip_str);

	if( !irc_ip )
	{// cannot resolve the IP address
		ShowError("Unable to resolve %s! Cannot connect to IRC server, disabling irc_bot.\n", irc_ip_str);
		irc.enabled = 0;
		return;
	}

	// perform the first connection attempt to the IRC server
	irc_connect_timer( 0, 0, 0, 0 );

	// register important timers for automated removal
	add_timer_func_list( irc_connect_timer, "irc_connect_timer" );
	add_timer_func_list( irc_keepalive_timer, "irc_keepalive_timer" );
	add_timer(gettick() + 30000, irc_keepalive_timer, 0, 0);
}

void do_final_irc(void)
{
}