<?php
  class UnregisteredRouteException extends Exception{
    public function __construct(){
      parent::__construct("Illegal call to unmapped route.");
    }
  }