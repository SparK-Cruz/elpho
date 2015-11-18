<?php
  abstract class StaticType{
    public final function __construct(){
      throw new Exception("Static classes cannot be instantiated.");
    }
  }
