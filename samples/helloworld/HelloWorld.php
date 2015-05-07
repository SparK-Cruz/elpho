<?php
  require("../../startup.php");

  class HelloWorld{
    public static final function main($args=array()){
      $word = new String("Hello World!");
      print($word);
    }
  }
