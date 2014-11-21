<?php
  class DatabaseException extends Exception{
    public function __construct($message){
      $this->message = $message;
    }
  }