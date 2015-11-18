<?php
  interface Embedable{
    public function getId();
    public function getTitle();
    public function getAuthor();
    public function getDescription();
    public function getPublishTime();
    public function getPublishDate();
    public function getTime();
    public function getTimeString();
    public function getFavorite();
    public function getViews();
    public function getUrl();
    public function getApi();
    public function getImage();
    public function getImageL();
    public function getImageM();
    public function getImageS();
    public function getLinkImage();
    public function getLinkImageL();
    public function getLinkImageM();
    public function getLinkImageS();
    public function getEmbedCode($width=586,$height=360);
    public function getPlayer();
  }
