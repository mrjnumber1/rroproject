<?php

	if( !is_logged_in() )
	{
		//Must be logged in to reg a new ingame account
		die();
	}