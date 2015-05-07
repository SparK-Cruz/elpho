<?php
  require("../../startup.php");

  require_once("php/lang/Object.php");

  class Events{
    public static final function main($args=array()){
      $dispatcher = new DispatcherHelper();
      $hardListener = new HardListener();
      $dynamicListener = function($event){
        print("<pre>");
        print("DynamicListener ".get_class($event)."'s target is ".$event->getTarget());
        print("</pre>");
      };

      $otherListener = new Object();
      $otherListener->ouvir = function($self, $event){
        echo $event->getName();
      };

      $dispatcher->addEventListener(EventHelper, array($hardListener,'listeningMethod'));
      $dispatcher->addEventListener(EventHelper, $dynamicListener);
      $dispatcher->addeventListener(EventHelper, array($otherListener, "ouvir"));

      $dispatcher->run();
    }
  }
