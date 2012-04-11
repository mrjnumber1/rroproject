// Copyright (c) Athena Dev Teams - Licensed under GNU GPL
// For more information, see LICENCE in the main folder

#ifndef _IRC_H_
#define _IRC_H_

//#include "map.h"
struct map_session_data;

// IRC .conf file [Zido]
#define IRC_CONF	"irc_athena.conf"

// IRC Access levels [Zido]
#define	ACCESS_OWNER	5
#define	ACCESS_SOP		4
#define	ACCESS_OP		3
#define	ACCESS_HOP		2
#define	ACCESS_VOICE	1
#define ACCESS_NORM		0

// maximum number of users available in a channel
#define MAX_CHANNEL_USERS	500

// maximum length for a received message, adjust accordingly for a busy channel
#define MAX_MESSAGE_LENGTH 512

// maximum length for a message being sent to the IRC server, adjust accordingly if necessary
#define MAX_SEND_LENGTH 256

// structure to store irc configuration
struct e_irc_config 
{
	unsigned char enabled;
	unsigned char autojoin;
	unsigned char announce_flag;
	unsigned char job_change_flag;
	unsigned char main_auto;
	unsigned char main_flag;
	unsigned char mvp_flag;
	unsigned char shop_flag;
};

// Config Variables
extern struct e_irc_config irc;

void irc_announce(const char* buf);
void irc_announce_main(const char* buf);
void irc_announce_jobchange(struct map_session_data *sd);
void irc_announce_shop(struct map_session_data *sd,int flag);
void irc_announce_mvp(struct map_session_data *sd, struct mob_data *md);

int irc_parse(int fd);
void do_final_irc(void);
void do_init_irc(void);
void irc_send(char *buf);
void irc_parse_sub(int fd, char *incoming_string);
int send_to_parser(int fd, char *input,char key[2]);

struct e_irc_session 
{
    int state;
    int fd;
    char username[30];
    char password[33];
};

typedef struct e_irc_session irc_session;

struct e_channel_data 
{
	struct 
	{
		char name[256];
		unsigned char level;
	} user[MAX_CHANNEL_USERS];
};

int parse_names_packet(char *str); // [Zido]
int parse_names(char *str); // [Zido]
int set_access(char *nick,int level); // [Zido]
int get_access(char *nick); // [Zido]
int irc_rmnames(void); // [Zido]
int irc_read_conf(char *file); // [Zido]

#endif /* _IRC_H_ */h