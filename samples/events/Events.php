<?php
  require("../../startup.php");

  require_once("php/lang/Object.php");
  require_once("DispatcherHelper.php");
  require_once("EventHelper.php");
  require_once("HardListener.php");

  class Events{
    public static final function main($args=array()){
      //Creating a object with powers to dispatch events (extends php.event.EventDispatcher)
      $dispatcher = new DispatcherHelper();

      //OPTION 1: Create a class to attach a method to the event
      $hardListener = new HardListener();

      //OPTION 2: Creates an anonymous function to serve as event listener
      $dynamicListener = function($event){
        print("<pre>");
        print("DynamicListener ".$event::getName()."'s target is ".$event->getTarget());
        print("</pre>");
      };

      //OPTION 3: Creates an dynamic object to attach a method to the event
      $otherListener = new Object();
      $otherListener->ouvir = function($self, $event){
        print("<pre>");
        print($self." listener method ".$event::getName()."'s target is ".$event->getTarget());
        print("</pre>");
      };

      //OPTION 1: Attaching the instance method as a listener to "EventHelper" event
      $dispatcher->addEventListener("EventHelper", array($hardListener,'listeningMethod'));

      //OPTION 2: Attaching the function listener to the "EventHelper" event
      $dispatcher->addEventListener("EventHelper", $dynamicListener);

      //OPTION 3: Attaching the dynamic instance method as a listener to "EventHelper" event
      $dispatcher->addeventListener("EventHelper", array($otherListener, "ouvir"));

      //The run method inside our dispatcher calls "dispatch(new EventHelper())" on self
      $dispatcher->run();
    }
  }
