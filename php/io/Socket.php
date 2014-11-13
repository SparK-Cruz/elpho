<?php
  import(php.lang.String);
  import(php.io.IoException);

  class Socket{
    private $handle;

    public function Socket($domain=AF_INET,$type=SOCK_STREAM,$protocol=SOL_TCP){
      $this->handle = $this->checkIO(socket_create($domain,$type,$protocol));
    }

    public function open($ip,$port=80){
      $this->checkIO(socket_connect($this->handle,$ip,$port));
    }
    public function close(){
      $this->checkIO(socket_close($this->handle));
    }

    public function setOption($level,$option,$value){
      socket_set_option($this->handle, $level, $option, $value);
    }
    public function read($length){
      return new String($this->checkIO(@socket_read($this->handle,$length,PHP_BINARY_READ)));
    }
    public function readTo($delimiters,$_=null){
      $delimiters = func_get_args();
      $result = new String();
      while(true){
        $char = $this->read(1);
        if(in_array($char->toString(),$delimiters) and $result->toString()) break;
        $result = $result->concat($char);

        foreach($delimiters as $delimiter){
          $leave = false;
          if($result->endsWith($delimiter)) $leave = true;
          $result = $result->replace($delimiter,'');
          if($leave) break 2;
        }
      }
      return $result;
    }
    public function readLine(){
      return $this->readTo("\r\n","\n","\r");
    }

    public function send($string,$flags=0){
      $length = strlen($string);
      $this->checkIO(socket_send($this->handle,$string,$length,$flags));
    }
    public function write($string){
      $length = strlen($string);
      $this->checkIO(socket_write($this->handle,$string,$length));
    }
    public function writeLine($string){
      $this->write($string.PHP_EOL);
    }
    public function flush(){
      $this->write(PHP_EOL);
    }

    public function tell($string){
      $this->writeLine($string);
      return $this->readLine();
    }

    private function checkIO($value){
      $last = 0;
      $message = "Unknown IO error.";
      if(is_resource($this->handle)){
        $last = socket_last_error($this->handle);
        $message = socket_strerror($last);
        socket_clear_error($this->handle);
      }
      if($value === false) throw new IoException($message." (".$last.")");
      return $value;
    }
  }
