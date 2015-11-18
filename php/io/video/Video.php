<?php
  require_once("php/lang/String.php");
  require_once("php/io/video/YouTubeVideo.php");
  require_once("php/io/video/VimeoVideo.php");
  require_once("php/io/IoException.php");

  abstract class Video{
    public static function create($id){
      $class = self::detectClass($id);
      return new $class($id);
    }
    private static function detectClass($id){
      $url = new String($id);
      if($url->contains("http")){
        if($url->contains("vimeo"))
          return "VimeoVideo";
        if($url->contains("youtube"))
          return "YouTubeVideo";

        throw new IoException("Video service not supported.");
      }

      switch($url->length()){
        case 11:
          return "YouTubeVideo";
        case 8:
          return "VimeoVideo";
        default:
          throw new IoException("Couldn't resolve ID string. Video service not supported.");
      }
    }
  }

