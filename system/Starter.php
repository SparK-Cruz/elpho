<?php
  final class Starter extends StaticType{
    private static $entryMethod;
    private static $exitMethod;
    private static $started = false;

    public static function start($path){
      if (self::$started) throw new Exception("Starter can only be run once!");
      $ignored = str_replace(".".PATH_SEPARATOR,'',get_include_path());
      LoadManager::loadElphoPath($path,$ignored);
      self::registerMain();

      register_shutdown_function(array(Starter,"callPrimaryMethods"));
      self::$started = true;
    }

    private static function registerEntry($method){
      self::$entryMethod = $method;
    }

    private static function registerExit($method){
      self::$exitMethod = $method;
    }

    private static function callEntry($args=array()){
      if(!self::$entryMethod) return;
      if(!method_exists(self::$entryMethod[0],self::$entryMethod[1])) return;
      call_user_func(self::$entryMethod,$args);
    }

    private static function callExit(){
      if(!self::$exitMethod) return;
      if(!method_exists(self::$exitMethod[0],self::$exitMethod[1])) return;
      call_user_func(self::$exitMethod);
    }

    private static function registerEntryClass($target){
      self::registerEntry(array($target,"main"));
      self::registerExit(array($target,"cleanUp"));
    }

    private static function fixShutdownScope(){
      chdir(dirname($_SERVER["SCRIPT_FILENAME"]));
    }

    public static function callPrimaryMethods(){
      try{
        self::fixShutdownScope();
        self::callEntry($_REQUEST);
        self::callExit();
      }catch(Exception $e){
        echo $e->getMessage();
      }
    }

    public static function registerMain($filename=null){
      if(!$filename) $filename = $_SERVER["SCRIPT_FILENAME"];
      $currentClass = basename($filename,".php");
      self::registerEntryClass($currentClass);
    }
  }
