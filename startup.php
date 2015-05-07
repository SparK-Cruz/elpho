<?php
  if(defined("DEBUG")){
    set_time_limit(DEBUG);
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $GLOBALS["startup"] = microtime(true);
  }

  require_once("system/topLevel.php");
  require_dir_once("php/lang");
  require_dir_once("system");

  Starter::start(dirname(__FILE__));

