
MT19937AR_OBJ = mt19937ar.o
MT19937AR_H = mt19937ar.h



#####################################################################
.PHONY : all clean help

all: $(MT19937AR_OBJ)

clean:
	rm -rf *.o

help:
	@echo "possible targets are 'all' 'clean' 'help'"
	@echo "'all'    - builds $(MT19937AR_OBJ)"
	@echo "'clean'  - deletes $(MT19937AR_OBJ)"
	@echo "'help'   - outputs this message"

#####################################################################

%.o: %.c $(MT19937AR_H)
	gcc -g -O2 -pipe -ffast-math -Wall -Wno-sign-compare -Wno-unused-parameter -Wno-pointer-sign -Wno-switch -fno-strict-aliasing -DHAVE_SETRLIMIT -DHAVE_STRNLEN -Wno-unused -Wno-parentheses -DHAVE_MONOTONIC_CLOCK  -L/usr/lib  -I../common -I/usr/include -c $(OUTPUT_OPTION) $<
