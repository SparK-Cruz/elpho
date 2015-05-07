<?php
  final class LoadManager extends StaticType{
    private static $ELPHO_PATH = "";
    private static $ignoredEntries = "?";
    private static $firstRunLoad = true;

    public static function loadElphoPath($path){
      if(!self::$firstTimeLoad) return;

      if(!self::$ELPHO_PATH){
        self::$ELPHO_PATH = $path;
      }

      self::addToIncludePath($path);
      self::$firstRunLoad = false;
    }
    public static function loadExtension($path){
      self::addToIncludePath($path);
      $file = $path."/startup.php";
      if(file_exists($file))
        require($file);
    }
    public static function requireDir($dir){
      self::requireDirInternal($dir, false);
    }
    public static function requireDirOnce($dir){
      self::requireDirInternal($dir, true);
    }
    private static function requireDirInternal($dir, $once=false){
      $handler = opendir($dir);
      while(($file = readdir($handler)) != null){
        if ($file[0] == ".")
          continue;

        if (basename($file) == basename($file, ".php"))
          continue;

        if (is_file($file)){
          if ($once){
            require_once($file);
            continue;
          }
          require($file);
        }
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
