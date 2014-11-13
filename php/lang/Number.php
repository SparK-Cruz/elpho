<?php
  import(php.lang.Math);
  import(php.lang.String);
  /**
   * Classe Number para PHP
   * @author Roger 'SparK' Rodrigues da Cruz
   * @company ValeMais Comunicação
   */
  class Number{
    private $value = "";
    private $double = false;

    public function Number($value,$float=false){
      if($float) $value = $value.".".$float;
      if($value === "The answer to life, the universe and everything") $value = 42;
      $this->double = strrpos($value,".");
      $this->value .= str_replace(".","",$value);
    }

    public function toHex(){
      $value = new String($this->value);
      $double = $this->double?$this->double:$value->length();
      $value = $value->substring(0,$double);

      $value = new String(self::convertHex($value));
      return $value->toUpperCase();
    }
    public function toOct(){
      $value = new String($this->value);
      $double = $this->double?$this->double:$value->length();
      $value = $value->substring(0,$double)->toString();
      return decoct($value);
    }

    public function toString(){
      $dot = ".";
      $double = $this->double;
      if(!$double){
        $dot = "";
        $double = strlen($this->value);
      }
      return substr($this->value,0,$double).$dot.substr($this->value,$double);
    }
    public function __toString(){
      return $this->toString();
    }

    private static function toHexChar($number){
      $chars = "ABCDEF";
      $offset = 10;
      if($number > 15) return false;
      if($number > 9) return $chars[$number-$offset];
      return $number;
    }
    private static function convertHex($number){
      if(!is_string($number) and !is_int($number)) $number = $number->toString();

      $left = Math::floor($number/16);
      $right = $number % 16;
      if($left > 15) $left = self::convertHex($left);
      return self::toHexChar($left).self::toHexChar($right);
    }
  }
