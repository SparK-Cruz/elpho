<?php
  require("../../startup.php");

  require_once("php/lang/Object.php");
  require_once("DispatcherHelper.php");
  require_once("EventHelper.php");
  require_once("HardListener.php");

  class Events{
    public static final function main($args=array()){
      $dispatcher = new DispatcherHelper();
      $hardListener = new HardListener();
      $dynamicListener = function($event){
        print("<pre>");
        print("DynamicListener ".$event::getName()."'s target is ".$event->getTarget());
        print("</pre>");
      };

      $otherListener = new Object();
      $otherListener->ouvir = function($self, $event){
        print("<pre>");
        print($self." listener method ".$event::getName()."'s target is ".$event->getTarget());
        print("</pre>");
      };

      $dispatcher->addEventListener(EventHelper, array($hardListener,'listeningMethod'));
      $dispatcher->addEventListener(EventHelper, $dynamicListener);
      $dispatcher->addeventListener(EventHelper, array($otherListener, "ouvir"));

      $dispatcher->run();
    }
  }
