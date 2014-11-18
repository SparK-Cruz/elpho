<?php
  class DatabaseException extends Exception{
    public __construct($message){
      $this->message = $message;
    }
  }