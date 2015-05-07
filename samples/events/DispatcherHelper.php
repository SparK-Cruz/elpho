<?php
  require_once("php/event/EventHandler.php");
  require_once("EventHelper.php");

  class DispatcherHelper extends EventHandler{
    public function DispatcherHelper(){}

    public function run(){
      $this->dispatchEvent(new EventHelper());
    }

    public function toString(){
      return "[object DispatcherHelper]";
    }
    public function __toString(){
      return $this->toString();
    }
  }
