<?php
  require("../../startup.php");

  class HelloWorld{
    public static function main($args=array()){
      $word = new String("Hello World!");
      print($word);
    }
  }
