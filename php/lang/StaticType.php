<?php
  abstract class StaticType{
    public final function __construct(){
      throw new Exception("ELPHO: Static classes cannot be instantiated.");
    }
  }
