<?php
  import(mvc.Controller);

  class ErrorController extends Controller{
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
  }
