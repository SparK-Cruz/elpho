<?php
  require_once('HelperService.php');

  class HelperTarget{
    private $service = null;

    public function __construct($helperService){
      $this->service = $helperService;
    }

    public function getResult(){
      return $this->service->getResult("something new");
    }

    public function injectableMethod($helperService){
      return $helperService->getResult("magic");
    }
  }