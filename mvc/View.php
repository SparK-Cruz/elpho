<?php
  class View extends Dynamic{
    private $sandboxedFileName;

    public function __construct($sandboxedFileName){
      $this->sandboxedFileName = $sandboxedFileName;
    }

    public function render(){
      call_user_func(function($viewbag){
        include($this->sandboxedFileName);
      }, $this);
    }
  }
