<?php
  class Find extends \database\event\Event{
    public function getName(){
      return "databaseFind";
    }
  }
?>