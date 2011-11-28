<?php
	final class Starter extends StaticType{
		private static $entryMethod;
		private static $exitMethod;
		
		public static function start(){
			$path = dirname(__FILE__);
			$defaultEntries = str_replace(".".PATH_SEPARATOR,'',get_include_path());
			set_include_path(".".PATH_SEPARATOR.$path.PATH_SEPARATOR.$defaultEntries);
			
			LoadManager::ignorePaths($defaultEntries);
			LoadManager::defineFolderMap("");
			
			self::registerMain();
			register_shutdown_function(array(Starter,"callPrimaryMethods"));
		}
		
		private static function registerEntry($method){
			self::$entryMethod = $method;
		}
		
		private static function registerExit($method){
			self::$exitMethod = $method;
		}
		
		private static function callEntry($args=array()){
			if(!self::$entryMethod) return;
			call_user_func(self::$entryMethod,$args);
		}
		
		private static function callExit(){
			if(!self::$exitMethod) return;
			call_user_func(self::$exitMethod);
		}
		
		private static function registerEntryClass($target){
			$entry = array($target,"main");
			$exit = array($target,"cleanUp");
			
			if(method_exists($entry[0],$entry[1]))
				self::registerEntry($entry);
			
			if(method_exists($exit[0],$exit[1]))
				self::registerExit($exit);
		}
		
		public static function callPrimaryMethods(){
			chdir(dirname($_SERVER["SCRIPT_FILENAME"]));
			self::callEntry($_REQUEST);
			self::callExit();
		}
		
		public static function registerMain($filename=null){
			if(!$filename) $filename = $_SERVER["SCRIPT_FILENAME"];
			$currentClass = basename($filename,".php");
			self::registerEntryClass($currentClass);
		}
	}
?>