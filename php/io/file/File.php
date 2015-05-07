<?php
  require_once("php/lang/String.php");

  class File{
    protected $nome;
    protected $conteudo;
    protected $tipo;
    protected $extensao;

    //constructor
    public function File($nome=""){
      if($nome != "") $this->setName($nome);

      if($this->exists()) $this->ler();
    }

    //set
    public function setName($nome){
      $nome = str_replace("\\","/",$nome);
      $this->nome = $nome;

      $ext = substr($nome,strrpos($nome,".")+1);
      $this->extensao = $ext;
      $this->tipo = self::toMime($ext);
    }
    public function setExtension($ext){
      $this->extensao = $ext;
      $this->tipo = self::toMime($ext);
    }
    public function setRawContent($conteudo){
      $this->conteudo = $conteudo;
    }
    public function setContent($conteudo){
      $this->conteudo = new String($conteudo);
    }
    public function setType($tipo){
      $this->tipo = $tipo;
      $this->extensao = self::toExtension($tipo);
    }
    public function setBase64($dataTag){
      $data = explode(";",$dataTag);
      $this->setType(str_replace("data:","",$data[0]));
      $this->setContent(base64_decode(str_replace("base64,","",$data[1])));
    }

    //get
    public function getFullName(){
      return new String($this->nome);
    }
    public function getName(){
      return new String(basename($this->getFullName()));
    }
    public function getExtension(){
      return new String($this->extensao);
    }
    public function getRawContent(){
      $content = $this->conteudo;
      if(is_object($content)) $content = $content->toString();
      return $content;
    }
    public function getContent(){
      return new String($this->conteudo);
    }
    public function exists(){
      return file_exists($this->nome);
    }
    public function getSize(){
      if(!is_object($this->conteudo)) $this->conteudo = new String($this->conteudo);
      return $this->conteudo->length();
    }
    public function getType(){
      return new String($this->tipo);
    }
    public function getInfo(){
      return new ArrayList($this->getType(),$this->getSize());
    }
    public function getBase64(){
      return "data:".$this->getType().";base64,".base64_encode($this->getContent());
    }
    public function getChecksum(){
      return md5($this->getContent()->toString());
    }
    public function copy(){
      $class = get_class($this);
      $copy = new $class;
      $copy->setType($this->getType());
      $copy->setContent($this->getContent());
      return $copy;
    }
    //actions
    public function clear(){
      $this->setContent('');
      if($this->exists()) $this->save();
    }
    public function refresh(){
      if($this->exists()) $this->ler();
    }
    public function write($texto){
      $this->conteudo .= $texto;
    }
    public function writeLine($texto){
      $this->write($texto.PHP_EOL);
    }
    public function save($nome=null){
      if($nome) $this->setName($nome);
      $arquivo = fopen($this->nome,'wb');
      fwrite($arquivo,$this->getContent());
      fclose($arquivo);
      chmod($this->nome,0755);
    }
    public function delete(){
      $this->clear();
      if($this->exists()) unlink($this->nome);
    }

    //extra
    protected function ler(){
      $this->conteudo = file_get_contents($this->nome);
    }

    public static function toMime($ext){
      $ext = strtolower($ext);
      $tipo = "application";
      switch($ext){
        case "jpg":
          $ext = "jpeg";
        case "png":
        case "gif":
        case "bmp":
          $tipo = "image";
        break;
        case "x-aiff":
          $tipo = "audio";
          $ext = "aiff";
        break;
        case "mp3":
          $tipo = "audio";
          $ext = "mpeg";
        break;
        case "wma":
          $tipo = "audio";
          $ext = "x-ms-wma";
        break;
        case "swf":
          $ext = "x-shockwave-flash";
        break;
        case "xml":
        case "atom":
          $ext = "atom+xml";
        break;
        case "js":
          $ext = "javascript";
        break;
        case "html":
        case "htm":
        case "stm":
          $ext = "html";
        case "css":
        case "csv":
          $tipo = "text";
        break;
        case "bas":
        case "h":
        case "c":
        case "txt":
          $ext = "plain";
          $tipo = "text";
        break;
      }

      return $tipo."/".$ext;
    }

    public static function toExtension($tipo){
      $tipo = strtolower($tipo);
      $tipo = explode("/",$tipo);
      $tipo = $tipo[1];
      $ext = "";
      switch($tipo){
        case "x-shockwave-flash":
          $ext = "swf";
        break;
        case "jpeg":
          $ext = "jpg";
        break;
        case "x-ms-wma":
          $ext = "wma";
        break;
        case "mpeg":
          $ext = "mp3";
        break;
        case "atom+xml":
          $ext = "xml";
        break;
        case "javascript":
          $ext = "js";
        break;
        case "plain":
          $ext = "txt";
        break;
        case "x-aiff":
          $ext = "aiff";
        break;
        default:
          $ext = $tipo;
        break;
      }
      return $ext;
    }
  }

