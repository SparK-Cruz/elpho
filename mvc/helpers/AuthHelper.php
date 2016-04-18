<?php
  class AuthHelper{
    public function __construct($authFormPath, $sessionFlag){
      return function($callable)use($authFormPath){
        if(!isset($sessionFlag) || !$sessionFlag){
          header("Location: ".Router::route($authFormPath));
          exit();
        }
        return $callable;
      };
    }
  }