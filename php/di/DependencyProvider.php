<?php
  interface DependencyProvider{
    static function getProvidedClassName();
    static function getInstance();
  }