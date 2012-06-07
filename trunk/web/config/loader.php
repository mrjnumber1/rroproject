<?php

// adapted from the PSR-0 standards

namespace config
{
	class loader
	{
		private $extension = '.php';
		private $namespace;
		private $include_path = '..\\jcp\\';
		private $namespace_separator = '\\';
		
		public function __construct()
		{
			$this->include_path = str_ireplace('\\', DIRECTORY_SEPARATOR, $this->include_path); 
		}
		
		public function register()
		{
			spl_autoload_register(array($this, 'load_class'));
		}
		public function unregister()
		{
			spl_autoload_unregister(array($this, 'load_class'));
		}
		public function load_class($class)
		{
			$class = ltrim($class, '\\');
			$file = '';
			$ns = '';
			
			if ($last_ns_pos = strripos($class, '\\') )
			{
				$ns = substr($class, 0, $last_ns_pos);
				$class = substr($class, $last_ns_pos+1);
				$file = str_replace('\\', DIRECTORY_SEPARATOR, $ns).DIRECTORY_SEPARATOR;
			}
			
			$file .= $class.'.php';
			

			require getcwd().DIRECTORY_SEPARATOR.$this->include_path.DIRECTORY_SEPARATOR.$file;
		}
		
	}
}