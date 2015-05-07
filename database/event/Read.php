<?php
  requireOnce("database/event/DatabaseEvent.php");

  class Read extends DatabaseEvent{
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