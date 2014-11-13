<?php
  class Create extends \database\event\Event{
    public function getName(){
      return "databaseCreate";
    }
  }
?>