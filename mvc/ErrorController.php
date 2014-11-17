<?php
  import(mvc.Controller);

  class ErrorController extends Controller{
    public static $e500view = "";
    public static $e404view = "";
    public static $e401view = "";

    public static final function e404($args){
      header("HTTP/1.1 404 Not found");

      if (self::$e404view === "")
        return self::renderDefault404();

      $view = new View(self::$e404view);
      $view->render();
      exit();
    }
    public static final function e401($args){
      header("HTTP/1.1 401 Access denied");

      if (self::$e401view === "")
        return self::renderDefault401($args["message"]);

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
        return self::renderDefault500($viewbag);

      $view = new View(self::$e500view);
      $view->type = $viewbag["type"];
      $view->message = $viewbag["message"];
      $view->stacktrace = $viewbag["stacktrace"];
      $view->render();
      exit();
    }

    private static final function renderDefault404(){
      echo '<!DOCTYPE html>'.
           '<html>'.
           '  <head>'.
           '    <meta charset="utf-8" />'.
           '    <title>404 Object not found</title>'.
           '  </head>'.
           '  <body>'.
           '    <h1>HTTP/1.1 404: Object not found</h1>'.
           '  </body>'.
           '</html>';
    }
    private static final function renderDefault401($message=""){
      echo '<!DOCTYPE html>'.
           '<html>'.
           '  <head>'.
           '    <meta charset="utf-8" />'.
           '    <title>401 Access denied</title>'.
           '  </head>'.
           '  <body>'.
           '    <h1>HTTP/1.1 401: Access denied</h1>'.
           '    <p>'.$message.'</p>'.
           '  </body>'.
           '</html>';
    }
    private static final function renderDefault500($viewbag){
      echo '<!DOCTYPE html>'.
           '<html>'.
           '  <head>'.
           '    <meta charset="utf-8" />'.
           '    <title>500 Internal server error</title>'.
           '  </head>'.
           '  <body>'.
           '    <h1>HTTP/1.1 500: Internal server error</h1>'.
           '    <h3>'.$viewbag["type"].'</h3>'.
           '    <p>'.$viewbag["message"].'</p>'.
           '  </body>'.
           '</html>';
    }
  }
