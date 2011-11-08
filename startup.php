<?php
	/**
	 * Arquivo de inicialização do Elpho framework
	 * Elpho = Extensão Lógica de PHP OO
	 * 
	 * v0.2
	 */
	if(defined("DEBUG")){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$GLOBALS["startup"] = microtime(true);
	}
	
	$elphoPath = dirname(__FILE__);
	
	$elphoDefaultEntries = str_replace(".".PATH_SEPARATOR,'',get_include_path());
	set_include_path(get_include_path().PATH_SEPARATOR.$elphoPath);
	
	require_once("LoadManager.php");
	LoadManager::defineFolderMap("",$elphoDefaultEntries);
	
	registerMain($_SERVER["SCRIPT_FILENAME"]);
	
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
	function named($constructor,$class,$parameters,$optional=0){
		$name = $class."_".$constructor;
		$params = array();
		$letter = 65;
		
		if($parameters + $optional > 52) throw new Exception("Exceded parameter limit for named constructors");
		
		for($i = 0; $i<$parameters; $i++){
			$params[] = '$'.chr($letter++);
			if($letter == 91) $letter = 97; //chegou no 'Z' +1 vai pro 'a'
		}
		for($i = 0; $i<$optional; $i++){
			$params[] = '$'.chr($letter++).'=null';
			if($letter == 91) $letter = 97;
		}
		
		$params = implode(",",$params);
		
		eval("class ".$name." extends ".$class."{ public function ".$name."(".$params."){ parent::".$name."(".$params."); } }");
	}
	function __autoload($classe){
		LoadManager::autoload($classe);
	}
?>