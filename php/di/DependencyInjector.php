<?php
  require_once('php/lang/Object.php');

  class DependencyInjector{
    private $providers = null;

    public function __construct(){
      $this->providers = new Object();
    }

    public function registerProvider($providerName){
      if(!class_exists($providerName))
        throw new Exception("Invalid dependency provider: '".$providerName."' not found");

      if(!in_array("DependencyProvider", class_implements($providerName)))
        throw new Exception("Invalid dependency provider: '".$providerName."' does not implement DependencyProvider interface");

      $className = call(array($providerName, 'getProvidedClassName'));

      $this->providers->{$className} = $providerName;
    }

    public function inject($target){
      $instance = null;

      if(is_string($target) && class_exists($target))
        $target = new ReflectionClass($target);

      if(is_array($target) && is_callable($target)){
        if(!is_string($target[0]))
          $instance = $target[0];
        $target = new ReflectionMethod($target[0], $target[1]);
      }

      $targetClass = null;
      if(is_a($target, "ReflectionClass")){
        $targetClass = $target;
        $target = $target->getConstructor();
      }

      if(!is_a($target, "ReflectionMethod"))
        throw new Exception("Could not inject dependencies for '".$target->getName()."'");

      $parameters = $target->getParameters();
      $injected = array();
      foreach($parameters as $parameter) {
        $injected[] = $this->buildDependency($parameter);
      }

      if($target->isConstructor())
        return $targetClass->newInstanceArgs($injected);

      return $target->invokeArgs($instance, $injected);
    }

    private function buildDependency(ReflectionParameter $parameter){
      //TODO upgrade to php7
      /**
      $type = $parameter->getType();
      if($type == null){
        if(!$parameter->allowsNull())
          throw new Exception("No injectable type specified for parameter '".$parameter->getName()."'");

        return null;
      }
      /*/
      $type = ucwords($parameter->getName());
      /**/

      if(!is_string($type))
        $type = $type->__toString();

      if($this->providers[$type] === null)
        throw new Exception("No dependency provider found for type '".$parameter->getName()."'");

      $providerName = $this->providers[$type];

      if(!class_exists($providerName))
        throw new Exception("Invalid dependency provider: '".$providerName."' not found");

      if(!in_array("DependencyProvider", class_implements($providerName)))
        throw new Exception("Invalid dependency provider: '".$providerName."' does not implement DependencyProvider interface");

      return call(array($providerName, "getInstance"));
    }
  }