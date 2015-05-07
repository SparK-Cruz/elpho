<?php
  requireOnce("php/event/Event.php");

  class EventHelper extends Event{
    public function getName(){
      return "EVENT_HELPER";
    }
  }
