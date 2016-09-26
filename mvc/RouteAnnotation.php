<?php
  require_once('php/lang/Annotation.php');

  class RouteAnnotation extends Annotation{
    protected static $name = "route";
    protected static $parameters = array("path", "method");
  }