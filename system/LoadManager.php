<?php
  final class LoadManager extends StaticType{
    private static $ELPHO_PATH = "";
    private static $ignoredEntries = "?";
    private static $firstTimeLoad = true;

    public static function loadElphoPath($path){
      if(!self::$firstTimeLoad) return;

      if(!self::$ELPHO_PATH){
        self::$ELPHO_PATH = $path;
      }

      self::addToIncludePath($path);
      self::$firstTimeLoad = false;
    }
    public static function loadExtension($path){
      self::addToIncludePath($path);
      $file = $path."/startup.php";
      if(file_exists($file))
        require($file);
    }
    public static function requireDirOnce($path){
      self::requireDirInternal($path, true);
    }
    private static function defineClassIfExists($file){
      $className = basename($file, ".php");
      if(class_exists($className) && !defined($className))
        define($className, $className);
    }
    private static function requireDirInternal($relative, $once=false){
      $isFramework = false;
      $framework = realpath(self::$ELPHO_PATH.'/'.$relative);
      $local = realpath($relative);

      $dir = $local;
      if(!file_exists($local)){
        $dir = $framework;
        $isFramework = true;
      }

      $handler = opendir($dir);
      if(!$handler)
        throw new Exception("Could not find path \"$relative\"!");

      while(($file = readdir($handler)) != null){
        if($file[0] == ".")
          continue;

        if(basename($file) == basename($file, ".php"))
          continue;

        $path = $relative."/".$file;

        if($isFramework){
          if(!is_file($framework.'/'.$file))
            continue;
        } else
          if(!is_file($path))
            continue;

        if($once){
          require_once($path);
          continue;
        }
        require($path);
      }
      closedir($handler);
    }

    private static function addToIncludePath($path){
      $entries = str_replace(".".PATH_SEPARATOR,'',get_include_path());
      set_include_path(".".PATH_SEPARATOR.$path.PATH_SEPARATOR.$entries);
    }

    private static function getIncludePath(){
      return explode(PATH_SEPARATOR, get_include_path());
    }
  }
