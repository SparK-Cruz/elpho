<?php
  requireOnce("database/event/DatabaseEvent.php");

  class Delete extends DatabaseEvent{
    public function getName(){
      return "databaseDelete";
    }
  }
?>