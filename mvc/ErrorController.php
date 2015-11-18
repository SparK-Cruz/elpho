<?php
  require_once("mvc/Controller.php");

  class ErrorController extends Controller{
    private static $default401 = "mvc/error/default401.html.php";
    private static $default404 = "mvc/error/default404.html.php";
    private static $default500 = "mvc/error/default500.html.php";

    public static $e500view = "";
    public static $e404view = "";
    public static $e401view = "";

    public static final function e404($args){
      header("HTTP/1.1 404 Not found");

      if (self::$e404view === "")
        self::$e404view = self::$default404;

      $view = new View(self::$e404view);
      $view->render();
      exit();
    }
    public static final function e401($args){
      header("HTTP/1.1 401 Access denied");

      if (self::$e401view === "")
        self::$e401view = self::$default401;

      $view = new View(self::$e401view);
      $view->message = $args["message"];
      $view->render();
      exit();
    }
    public static final function e500($args){
      header("HTTP/1.1 500 Internal server error");

      $exception = $args["exception"];
      $viewbag = array(
        "type" => get_class($exception),
        "message" => $exception->getMessage(),
        "stacktrace" => $exception->getTraceAsString()
      );

      if (self::$e500view === "")
        self::$e500view = self::$default500;

      $view = new View(self::$e500view);
      $view->type = $viewbag["type"];
      $view->message = $viewbag["message"];
      $view->stacktrace = $viewbag["stacktrace"];
      $view->render();
      exit();
    }
  }
