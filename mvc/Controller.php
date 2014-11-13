<?php
  abstract class Controller{
    protected static function redirect($url){
      if (is_object($url) and is_a($url,"Route"))
        $url = $url->getPath();

      $route = Router::route($url);
      header("Location: ".$route);
    }

    protected static function denyAccess($message=''){
      $args = $_REQUEST;
      $args["message"] = $message;
      call_user_func(array(ErrorController, "e401"), $args);
    }
  }
