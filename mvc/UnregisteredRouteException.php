<?php
  class UnregisteredRouteException extends Exception{
    public function __construct(){
      parent::__construct("ELPHO: Illegal call to unmapped route.");
    }
  }