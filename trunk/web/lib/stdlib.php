<?php

	namespace lib
	{
		error_reporting(-1);
		//xdebug_enable();
		//set_exception_handler('\\lib\\errors\\log');
		$timer = new \lib\timer\timer;
		$timer->set_time();
	}

	namespace lib\arrays
	{
		function flatten($array, $flat = false)
		{
			if ( !is_array($array) || empty($array) )
            {
				return '';
            }
			
			if ( empty($flat) )
            {
				$flat=array();
            }
				
			foreach ($array as $key => $val)
			{
				if ( is_array($val) )
                {
					$flat   = flatten($val, $flat);
                }
				else
					$flat[] = $val;
			}

			return $flat;
		}
	}

	namespace lib\debug
	{
		// get_backtrace pulled from php.net commenter
		function get_backtrace($traces_to_ignore = 1) // to ignore this call
		{
            $ret = array();

		    $traces = debug_backtrace();

			foreach ($traces as $i => $call)
			{
				if ($i < $traces_to_ignore )
                {
					continue;
                }

				$object = '';
				if (isset($call['class']))
                {
					$object = $call['class'].$call['type'];

					if (is_array($call['args']))
                    {
						foreach ($call['args'] as &$arg)
                        {
							get_arg($arg);
						}
					}
				}       

				$ret[] = '#'.str_pad($i - $traces_to_ignore, 3, ' ')
				    .$object.$call['function'].'('.implode(', ', $call['args'])
				    .') called at ['.$call['file'].':'.$call['line'].']';
			}

			return implode("\n",$ret);
		}
		
		function print_s($variable, $printed = true)
        {
            $str = nl2br(html_entities(str_replace(' ', '&nbsp;',print_r($variable,true) ) ) );

            if($printed)
            {
                echo $str;
            }

            return $str;
        }
	}
	namespace lib\timer
	{
		function current_timestamp()
        {
            return date('Y-m-d H:i:s');
        }
		
		class timer
		{
			private $start_time=0;
			
			public function set_time()
            {
                $this->start_time = microtime(true);
            }
			public function get_time()
            {
                return $this->start_time;
            }
			public function get_elapsed()
            {
                return round(microtime(true)-$this->start_time, 3);
            }
		}
	}
	
	namespace lib\string
	{
		function right($string,$count)
        {
            return substr($string, ($count * -1) );
        }
		function left($string,$count)
        {
            return substr($string, 0, $count);
        }
	}
	
