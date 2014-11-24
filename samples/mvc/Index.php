<?php
  require("../../startup.php");

  import(mvc);
  import(controllers);

  class Index{
    public static final function main($args=array()){
      $router = Router::getInstance(dirname(__FILE__));

      //** Sample error view customization
      //ErrorController::$e500view = "e500.html";
      //ErrorController::$e404view = "e404.html";
      //ErrorController::$e401view = "e401.html";

      self::mapRoutes($router);

      $router->findRoute()->go($router->getRequest());
    }

    private static function mapRoutes($router){
      //** Sample "get" route
      $router->map(array("", "home"), array(Home, "index"));
      $router->map("contact", array(Home, "contact"));

      //** Sample "post" route, other http methods work too
      $router->map("contact", array(Home, "sendEmail"), "post");

      //** Sample route with parameters
      //$router->map("blog/*post", array(Blog, "post"));
      //$router->map("blog/page/#page", array(Blog, "list"));
      //These will produce $args["post"] and $args["page"] respectively in their controllers' methods
    }
  }