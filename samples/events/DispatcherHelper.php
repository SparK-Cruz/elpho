<?php
  requireOnce("php/event/EventHandler.php");
  requireOnce("php/lang/Object.php");

  class DispatcherHelper extends Object{
    public function DispatcherHelper(){}

    public function run(){
      $this->dispatchEvent(new EventHelper());
    }
  }
