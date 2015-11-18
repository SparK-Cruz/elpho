<?php
  require_once("database/event/DatabaseEvent.php");

  class Update extends DatabaseEvent{
    public function getName(){
      return "databaseUpdate";
    }
  }
?>