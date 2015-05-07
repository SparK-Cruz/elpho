<?php
  requireOnce("database/event/DatabaseEvent.php");

  class Find extends DatabaseEvent{
    public function getName(){
      return "databaseFind";
    }
  }
?>