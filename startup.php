<?php
  if(defined("DEBUG")){
    set_time_limit(DEBUG);
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $GLOBALS["startup"] = microtime(true);
  }

  require_once("system/topLevel.php");
  require_once("php/lang/StaticType.php");
  require_once("system/LoadManager.php");
  require_once("system/Starter.php");

  Starter::start(__DIR__);

  requireDirOnce("php/lang");

