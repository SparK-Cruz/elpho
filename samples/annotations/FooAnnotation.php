<?php
  require_once('php/lang/Annotation.php');

  class FooAnnotation extends Annotation{
    protected static $name = "foo";
    protected static $parameters = array("bar", "baz");
  }