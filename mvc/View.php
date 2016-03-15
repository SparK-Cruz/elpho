<?php
  class View extends Dynamic{
    private $sandboxedFileName;

    public function __construct($sandboxedFileName){
      $this->sandboxedFileName = $sandboxedFileName;
    }

    public function render($model=null){
      call_user_func(function($viewbag,$model){
        ob_start();
        if(!include($this->sandboxedFileName)){
          ob_end_clean();
          throw new Exception("ELPHO: Unimplemented view \"$this->sandboxedFileName\".");
        }
        ob_end_flush();
      }, $this, $model);
    }
  }
