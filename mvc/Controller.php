<?php
  abstract class Controller{
    protected static function redirect($url){
      if(is_object($url) and is_a($url,"Route"))
        $url = $url->getPath();

      $route = Router::route($url);
      header("Location: ".$route);
    }

    protected static function allowMethods($methods=array()){
      if(!is_array($methods))
        $methods = func_get_args();

      $requestMethod = "get";
      if (isset($_SERVER["REQUEST_METHOD"]))
        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);

      foreach($methods as $method){
        if(strtolower($method) == strtolower($requestMethod))
          return;
      }

      throw new MethodNotAllowedException();
    }

    protected static function denyAccess($message=''){
      $args = $_REQUEST;
      $args["message"] = $message;
      call(array(ErrorController, "e401"), $args);
    }
  }
