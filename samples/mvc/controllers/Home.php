<?php
  class Home extends Controller{
    public static final function index($args){
      $view = new View("views/home/index.html.php");
      $view->render();
    }

    public static final function contact($args){
      $view = new View("views/home/contact.html.php");
      $view->resultMessage = "";
      $view->render();
    }

    public static final function sendEmail($args){
      self::allowMethods("post");

      $view = new View("views/home/contact.html.php");
      $view->resultMessage = "E-mail was not sent. But let's pretent it was ;)";
      $view->render();
    }
  }