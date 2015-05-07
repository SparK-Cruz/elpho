<?php
  requireOnce("database/event/DatabaseEvent.php");

  class Create extends DatabaseEvent{
    public function getName(){
      return "databaseCreate";
    }
  }
?>