<?php
  abstract class Event extends \php\event\Event{
    private $statement;
    private $options;

    public function DatabaseEvent($statement,$options){
      $this->statement = $statement;
      $this->options = $options;
    }
    public function getStatement(){
      return $this->statement;
    }
    public function getOptions(){
      return $this->options;
    }
  }
?>