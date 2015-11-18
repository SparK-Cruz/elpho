<?php
  require_once("database/event/DatabaseEvent.php");

  class Find extends DatabaseEvent{
    public function getName(){
      return "databaseFind";
    }
  }
?>