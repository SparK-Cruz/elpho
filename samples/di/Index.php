<?php
  require('../../startup.php');
  require_once('php/di/DependencyInjector.php');

  require_once('HelperServiceProvider.php');
  require_once('HelperTarget.php');

  class Index{
    public static function main($args=array()){
      $di = new DependencyInjector();
      $di->registerProvider('HelperServiceProvider');

      $target = $di->inject('HelperTarget');
      $target2 = $di->inject(new ReflectionClass('HelperTarget'));

      echo "t1: ".$target->getResult()."<br/>";
      echo "t2: ".$target2->getResult()."<br/>";
      echo "t1m: ".$di->inject(array($target,'injectableMethod'));
    }
  }