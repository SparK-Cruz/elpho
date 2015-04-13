<?php
  import(php.lang.String);
  import(mvc.Route);
  import(mvc.ErrorController);

  class Router{
    private static $instance = null;
    private static $root;
    private static $default;

    private $routes = array();

    public static function getInstance($index=null, $default=array(ErrorController, "e404")){
      if(self::$instance == null){
        if($index == null)
          throw new Exception("ELPHO: No index for new Router.");

        $routes["get"] = array();

        $routes["put"] = array();
        $routes["post"] = array();
        $routes["delete"] = array();

        self::$instance = new self($index, $default);
      }
      return self::$instance;
    }
    public static function fileRoute($uri){
      $uri = new String($uri);
      if(!$uri->startsWith("/"))
        $uri = new String("/".$uri);

      $router = self::getInstance();

      return self::$root.$uri;
    }
    public static function route($uri="", $method="get"){
      if (is_object($uri) and is_a($uri, Route)){
        if (!in_array($uri, $this->routes[$method]))
          throw new UnregisteredRouteException();

        $uri = $uri->getPath();
      } else {
        $uri = new String($uri);
        if(!$uri->startsWith("/"))
          $uri = new String("/".$uri);

        $router = self::getInstance();

        if($router->findRoute($uri->split("/")->filter(), $method) == self::$default)
          return self::$root."/error/404";
      }

      return self::$root.$uri;
    }

    private function __construct($index, $default){
      $default = $default;
      $index = new String($index);
      $docRoot = new String($_SERVER["DOCUMENT_ROOT"]);

      self::$default = new Route("", $default);
      self::$root = $index->replace("\\", "/")->replace($docRoot->replace("\\","/")->toString(), "")->toLowerCase();
    }

    public function map($url, $callback, $method="get"){
      if(is_array($url)){
        foreach ($url as $value) {
          $this->map($value, $callback, $method);
        }
        return;
      }

      if(!is_object($url))
        $url = new String($url);

      $parts = $url->replace("\\", "/")->split("/");
      $parts = $parts->filter();
      $this->routes[$method][] =  new Route($parts, $callback);
    }
    public function mapResource($baseUrl, $controller){
      if(!is_object($baseUrl))
        $baseUrl = new String($baseUrl);

      $parts = $baseUrl->replace("\\", "/")->split("/");
      $parts = $parts->filter();
      $baseUrl = $parts->join("/")->concat("/");

      $this->map($baseUrl, array($controller, "index"));
      $this->map($baseUrl."new", array($controller, "new"));
      $this->map($baseUrl."#:id", array($controller, "view"));
      $this->map($baseUrl."#:id/edit", array($controller, "edit"));

      $this->map($baseUrl."#:id", array($controller, "put_edit"), "put");
      $this->map($baseUrl."create", array($controller, "create"), "post");
      $this->map($baseUrl."#:id", array($controller, "delete"), "delete");
    }

    public function getRequest(){
      $requestUri = new String();
      if (isset($_SERVER["REQUEST_URI"]))
        $requestUri = new String($_SERVER["REQUEST_URI"]);

      if ($requestUri->toLowerCase()->startsWith(self::$root))
        $requestUri = $requestUri->substr(strlen(self::$root));

      $request = $requestUri->split("/")->filter();
      return $request;
    }
    public function findRoute($request=null, $method=null){
      if($request === null or $request == array())
        $request = $this->getRequest();

      $routes = array();
      if ($method == null)
        if (isset($_SERVER['REQUEST_METHOD']))
          $method = strtolower($_SERVER['REQUEST_METHOD']);
        else
          $method = "get";

      if (isset($this->routes[$method]))
        $routes = $this->routes[$method];

      foreach($routes as $route){
        if(!$route->match($request))
          continue;

        return $route;
      }

      return self::$default;
    }

    public function serve() {
      $this->findRoute()->go($this->getRequest());
    }
  }
