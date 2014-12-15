<?php
  class HardListener{
    public function listeningMethod($event){
      print("<pre>");
      print("HardListener ".get_class($event)."'s target is ".$event->getTarget());
      print("</pre>");
    }
  }
