<?php
	/**
	 * Arquivo de inicialização do Elpho framework
	 * Elpho = Extensão Lógica de PHP OO
	 * 
	 * v0.2
	 */
	if(defined("DEBUG") or true){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$GLOBALS["startup"] = microtime(true);
	}
	
	require_once("php/lang/StaticType.php");
	require_once("Starter.php");
	require_once("LoadManager.php");
	require_once("topLevel.php");
	
	Starter::start();
	
	register_shutdown_function(array(Starter,"callPrimaryMethods"));
?>