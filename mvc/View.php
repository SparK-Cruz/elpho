<?php
  class View extends Dynamic{
    private $sandboxedFileName;

    public function __construct($sandboxedFileName){
      $this->sandboxedFileName = $sandboxedFileName;
    }

    public function render($model=null){
      call_user_func(function($viewbag,$model){
        include($this->sandboxedFileName);
      }, $this, $model);
    }
  }
