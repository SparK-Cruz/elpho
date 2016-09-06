<?php
  class Request{
    private $link;

    public function Request($url=null,$options=array()){
      if(!function_exists("curl_init")) throw new Exception("Client URL Module not found in current environment.");
      $this->link = $this->checkSuccess(curl_init($url));
      $this->setOption("RETURNTRANSFER",true);
      $this->setOptions($options);
    }
    public function setHandle($handle){
      $this->link = $handle;
    }
    public function setOption($option,$value){
      $this->checkSuccess(curl_setopt($this->link, constant("CURLOPT_".$option), $value));
    }
    public function execute(){
      return $this->checkSuccess(curl_exec($this->link));
    }
    public function setOptions($options){
      foreach($options as $key => $value){
        $this->setOption($key,$value);
      }
    }
    public function copy(){
      return self::create($this->checkSuccess(curl_copy_handle($this->link)));
    }
    public function close(){
      $this->checkSuccess(curl_close($this->link));
    }
    public function __destruct(){
      $this->close();
    }

    public static function create($handle){
      $new = new Request();
      $new->setHandle($handle);
      return $new;
    }

    private function checkSuccess($value){
      if($value !== false and $value !== null) return $value;

      $message = "Invalid Client URL handler, check your PHP setup and the libcurl extension.";
      if($this->link) $message = curl_error($this->link);
      throw new IoException($message);
    }
  }
