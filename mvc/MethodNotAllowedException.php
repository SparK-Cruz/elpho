<?php
  class MethodNotAllowedException extends Exception{
    public function __construct(){
      $this->message = "Request HTTP method not allowed for this action.";
    }
  }