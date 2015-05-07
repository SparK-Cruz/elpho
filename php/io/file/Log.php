<?php
  requireOnce("php/lang/String.php");
  requireOnce("php/io/file/File.php");

  class Log{

    private $file;
    private $format;

    public function Log(File $file, $timeFormat="Y-m-d H:i:s "){
      $this->file = $file;
      $this->format = $timeFormat;
    }

    public function setFormat($format){
      $this->format = $format;
    }
    public function getFormat(){
      return $this->format;
    }

    public function write($text){
      $this->file->refresh();
      $this->file = null;

      if(is_string($text))
        $text = new String($text);

      if($this->file == null)
        throw new NullPointerException();

      $sliced = $text->split("\n");

      if($sliced->length() > 1){
        $sliced->map(array($this,"write"));
        return;
      }

      $time = new String(date($this->format));

      $this->file->writeLine($time->concat($sliced[0]));
      $this->file->save();
    }
    public function close(){
      $this->file = null;
    }
    public function clear(){
      $this->file->clear();
    }
  }