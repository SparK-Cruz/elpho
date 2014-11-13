<?php
  class Read extends \php\event\Event{
    private $index;

    public function Read($index){
      $this->index = $index;
    }
    public function getIndex(){
      return $this->index;
    }
    public function getName(){
      return "databaseRead";
    }
  }
?>