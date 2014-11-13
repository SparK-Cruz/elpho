<?php
  class Update extends \database\event\Event{
    public function getName(){
      return "databaseUpdate";
    }
  }
?>