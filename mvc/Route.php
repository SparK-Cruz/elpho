<?php
  class Route{
    const ARG_TYPE_NUMBER = "ARG_TYPE_NUMBER";
    const ARG_TYPE_ANY = "ARG_TYPE_ANY";

    private $callback;
    private $path;
    private $argsIndexes;
    private $argsNames;
    private $args;

    public function __construct($path, $callback){
      if(is_array($path))
        $path = new ArrayList_from($path);

      if(!is_object($path)){
        $path = new String($path);
        $path = $path->replace("\\", "/")->split("/");
      }

      $path = $path->filter();

      $this->callback = $callback;
      $this->path = $path;

      $this->argsIndexes = new ArrayList();
      $this->argsNames = new ArrayList();

      for($i=0; $i<$path->length(); $i++){
        $part = $path[$i];

        if($part->startsWith("*")){
          $this->argsIndexes->push(array($i, self::ARG_TYPE_ANY));
          $this->argsNames->push($part->substr(1));
        }

        if($part->startsWith("#")){
          $this->argsIndexes->push(array($i, self::ARG_TYPE_NUMBER));
          $this->argsNames->push($part->substr(1));
        }
      }
    }
    private function readArgs($request){
      $args = new Object();
      parse_str(file_get_contents("php://input"),$inputData);

      foreach($this->argsIndexes as $key => $i){
        if($i[0] >= $request->length())
          continue;

        $args->{$this->argsNames[$key]} = $request[$i[0]];
      }

      foreach($inputData as $key => $value){
        $args->{$key} = $value;
      }

      return $args;
    }
    public function getPath(){
      return $this->path;
    }
    public function match(ArrayList $path){
      for($i=0; $i<max($this->path->length(),$path->length()); $i++){
        if(!isset($this->path[$i]))
          return false;

        $local = $this->path[$i];

        if(!isset($path[$i]) or $path[$i]->isEmpty())
          return false;

        $request = $path[$i];

        if($this->argsIndexes->contains(array($i, self::ARG_TYPE_NUMBER))
          and is_numeric($request->toString()))
          continue;

        if($this->argsIndexes->contains(array($i, self::ARG_TYPE_ANY)))
          continue;

        if(!$local->equals($request))
          return false;
      }

      return true;
    }

    public function go($request){
      try{
        $args = $this->readArgs($request);
        $signal = call(array($this->callback[0], "_beforeFilter"), $this->callback[1], $args);
        if($signal)
          call($this->callback, $args);

      }catch (Exception $ex){
        call(array("ErrorController", "e500"), array("exception"=>$ex));
      }
    }
  }
