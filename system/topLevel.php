<?php
  function registerMain($filename=null){
    Starter::registerMain($filename);
  }
  function loadExtension($path){
    LoadManager::loadExtension($path);
  }

  function requireOnce($file){
    LoadManager::requireOnce($file);
  }
  function requireFile($file){
    LoadManager::requireFile($file);
  }
  function requireDirOnce($dir){
    LoadManager::requireDirOnce($dir);
  }
  function requireDir($dir){
    LoadManager::requireDir($dir);
  }

  function call($callback,$args=null,$_=null){
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array($callback,$args);
  }
  function callArgs($callback,$args){
    return call_user_func_array($callback,$args);
  }
  function alias($new,$old){
    eval("class ".$new." extends ".$old."{}");
  }
  function named($constructor,$class=null){
    if(!$class){
      $trace = debug_backtrace();
      $class = basename($trace[0]["file"],".php");
    }

    $name = $class."_".$constructor;
    eval("class ".$name." extends ".$class."{ public function __construct(){ call_user_func_array(array('parent','_".$constructor."'), func_get_args()); } }");
  }

  function matchTypes($type,$_=null){
    $trace = debug_backtrace();
    $types = func_get_args();
    $arguments = $trace[1]["args"];

    $i = -1;
    foreach($types as $type){
      $i++;
      $argument = $arguments[$i];

      if(is_object($argument)){
        if(!is_a($argument,$type)) return false;
        continue;
      }

      if(gettype($argument) !== $type) return false;
    }
    return true;
  }
