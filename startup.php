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
	require_once("topLevel.php");
	
	LoadManager::defineFolderMap("",$elphoDefaultEntries);
	registerMain($_SERVER["SCRIPT_FILENAME"]);
?>