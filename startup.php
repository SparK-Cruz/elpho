<?php
	if(defined("DEBUG")){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$GLOBALS["startup"] = microtime(true);
	}
	
	require_once("php/lang/StaticType.php");
	require_once("Starter.php");
	require_once("LoadManager.php");
	require_once("topLevel.php");
	
	Starter::start();
?>