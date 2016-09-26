<?php
  class HelperService{
    private $number = 0;
    private $parameter = null;

    public function __construct($number, $parameter=null){
      $this->number = $number;
      $this->parameter = $parameter;
    }

    public function getResult($local){
      return "Injected with parameter ".$this->parameter." and called with ".$local." (Service instance ".$this->number.")";
    }
  }