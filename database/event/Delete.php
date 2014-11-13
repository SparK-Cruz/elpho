<?php
  class Delete extends \database\event\Event{
    public function getName(){
      return "databaseDelete";
    }
  }
?>