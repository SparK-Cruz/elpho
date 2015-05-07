<?php
  class HardListener{
    public function listeningMethod($event){
      print("<pre>");
      print("HardListener ".$event::getName()."'s target is ".$event->getTarget());
      print("</pre>");
    }
  }
