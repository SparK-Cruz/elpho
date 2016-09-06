<?php
  abstract class Helper{
    protected $view;
    public function __construct(View $view){
      $this->view = $view;
    }
  }