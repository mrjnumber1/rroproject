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
		define('PRINT_ERRORS', true);

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

		function get_arg(&$arg)
		{
			if (is_object($arg))
			{
				$arr = (array)$arg;
				$args = array();
				foreach ($arr as $key => $value)
				{
					if (strpos($key, chr(0)) !== false)
					{
						$key = '';    // Private variable found
					}
					$args[] =  '['.$key.'] => '.get_arg($value);
				}

				$arg = get_class($arg) . ' Object ('.implode(',', $args).')';
			}
		}
		
		function print_s($variable, $printed = true)
        {
			if ( !defined('PRINT_ERRORS') )
			{
				\dao\db::log_error('Attempted to print '.$variable.' with error printing off. Var data: '.print_r($variable, true), false );
				return '';
			}

            $str = nl2br(htmlentities(str_replace(' ', '&nbsp;',print_r($variable,true) ) ) );

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
		function duration(\DateTime $start, \DateTime $end)
		{

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
		function right($string, $count)
        {
            return substr($string, ($count * -1) );
        }

		function left($string,$count)
        {
            return substr($string, 0, $count);
        }

		function number_format($amount, $decimals = 0)
		{
			// TODO: create modifs based on logged in member language
			$dec_point = '.';
			$thousands_sep = ',';

			return number_format($amount, $decimals, $dec_point, $thousands_sep);
		}

		function percent($amount, $symbol = false, $precision = 2)
		{
			return (round($amount, $precision).($symbol?'%':''));
		}
	}
	
