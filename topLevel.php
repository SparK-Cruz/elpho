<?php
	function elphoCallEntryMethod($target,$args,$currentFile){
		chdir(dirname($currentFile));
		
		$entry = array($target,"main");
		$exit = array($target,"cleanUp");
		
		if(method_exists($entry[0],$entry[1]))
			call_user_func($entry,$args);
		
		if(method_exists($exit[0],$exit[1]))
			call_user_func($exit);
	}
	
	function registerMain($filename=null){
		if(!$filename) $filename = $_SERVER["SCRIPT_FILENAME"];
		$elphoCurrentClass = basename($filename,".php");
		register_shutdown_function("elphoCallEntryMethod",$elphoCurrentClass,$_REQUEST,$filename);
	}
	function import($root=false){
		LoadManager::import($root);
	}
	function alias($new,$old){
		eval("class ".$new." extends ".$old."{}");
	}
	function named($constructor,$class){
		$name = $class."_".$constructor;
		eval("class ".$name." extends ".$class."{ public function ".$name."(){ call_user_func_array(array(parent,".$name."), func_get_args()); } }");
	}
	function __autoload($classe){
		LoadManager::autoload($classe);
	}
?>