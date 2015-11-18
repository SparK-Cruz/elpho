<?php
  require_once("php/event/EventHandler.php");
  require_once("php/lang/Object.php");

  class DispatcherHelper extends Object{
    public function DispatcherHelper(){}

    public function run(){
      $this->dispatchEvent(new EventHelper());
    }
  }
