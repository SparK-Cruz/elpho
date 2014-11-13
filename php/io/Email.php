<?php
   /**
   * @package php.io
   * @author SparK
   */
  import(php.lang.String);
  import(php.lang.ArrayList);
  import(php.io.IoException);
  import(php.io.file.File);

  class Email{
    private $charset;
    private $boundary;
    private $boundaryClose;

    private $origem;
    private $nomeOrigem;
    private $destino;
    private $assunto;
    private $mensagem;
    private $destinosPorEmail;
    private $headers;
    private $anexos;

    //constructor
    public function Email(){
      $headers = "MIME-Version: 1.0".PHP_EOL;
      $headers .= 'Content-Type: multipart/mixed; boundary="{boundary}";'.PHP_EOL;
      $headers .= 'From: "{nomeOrigem}" <{origem}>'.PHP_EOL;
      $headers .= 'Reply-To: {origem}'.PHP_EOL;
      $headers .= '{extra}'.PHP_EOL;
      $this->headers = $headers;

      $this->charset = "utf-8";
      $this->boundary = "bound".md5(base64_encode(date("YYmYmdYmdHYmdHiYmdHis")))."bound"; //something random so nobody types it
      $this->destinosPorEmail = 15;
      $this->anexos = new ArrayList();
      $this->assunto = "undefined";
      $this->mensagem = "undefined";
      $this->origem = "webmaster@".str_replace("www.","",isset($_SERVER["HTTP_HOST"])?:$_SERVER["SERVER_NAME"]);
    }

    //set
    public function setOrigin($origin){
      $this->origem = $origin;
    }
    public function setOriginName($name){
      $this->nomeOrigem = $name;
    }
    public function setDestiny($destiny){
      $destino = explode(",",$destiny);
      $final = array();
      foreach($destino as $email){
        if($email == "") continue;
        $final[] = trim($email);
      }
      $this->destino = implode(", ",$final);
    }
    public function setSubject($subject){
      $this->assunto = $subject;
    }
    public function setMessage($message){
      $this->mensagem = $message;
    }
    public function setMaxRecipientsByMail($limit){
      $this->destinosPorEmail = $limit;
    }
    public function setCharset($charset){
      $this->charset = $charset;
    }

    //get
    public function getOrigin(){
      return $this->origem;
    }
    public function getOriginName(){
      return $this->nomeOrigem;
    }
    public function getDestiny(){
      return $this->destino;
    }
    public function getSubject(){
      return $this->assunto;
    }
    public function getMessage(){
      return $this->mensagem;
    }
    public function getMaxRecipientsByMail(){
      return $this->destinosPorEmail;
    }
    public function getCharset(){
      return $this->charset;
    }

    public function hasAttachments(){
      return ($this->anexos->length() > 0);
    }

    //actions
    public function addAttachment($file){
      if(is_string($file)) $file = new File($file);
      $this->anexos->push($file);
      return $file->getChecksum();
    }
    public function removeAttachment($index){
      if(is_string($index)){
        $i = 0;
        for($i=0; $i<count($this->anexos); $i++){
          $found = true;
          if($anexos[$i]->getName() == $index) break;
          $md5 = $anexos[$i]->getChecksum();
          if($md5 == $index) break;
          $found = false;
        }
        $index = $i;
      }

      if(isset($this->anexos[$index])) $this->anexos->splice($index,1);
    }

    public function send(){
      $mensagem = $this->gerarResultado();

      $para = new ArrayList();
      $pagina = 0;
      $destino = new String($this->destino);
      $destino = $destino->split(',');

      for($i = 0; $i<$destino->length(); $i++){
        $para->push(trim($destino[$i]));

        if(!$this->checkEstourouLimite($i,$destino->length(),$pagina)) continue;

        $assunto = self::fixAcentoAssunto($this->assunto);
        $sucesso = @mail($para->join(',')->toString(),$assunto,$mensagem,$this->headers);
        if(!$sucesso) throw new IoException("Unexpected error from E-mail server.");
        $para = new ArrayList();
        $pagina++;
      }
    }

    //extra
    private function gerarAnexos(){
      $anexos = new ArrayList();
      $count = 0;
      foreach($this->anexos as $anexo){
        $count++;
        $html = array();
        $html[] = 'Content-Type: '.$anexo->getType().PHP_EOL.' name="'.$anexo->getName().'"';
        $html[] = 'Content-Disposition: attachment; filename="'.$anexo->getName().'"';
        $html[] = 'Content-ID: <'.$anexo->getName().'@1000>';
        $html[] = 'Content-transfer-encoding:base64'.PHP_EOL;
        $html[] = self::gerarBaseAnexo($anexo);

        $anexos->push(implode(PHP_EOL,$html).PHP_EOL);
      }

      return $anexos;
    }
    private static function fixAcentoAssunto($assunto){
      return mb_encode_mimeheader($assunto,"UTF-8","B",PHP_EOL);
    }
    private static function gerarBaseAnexo($arquivo){
      return chunk_split(base64_encode($arquivo->getContent()));
    }
    private function gerarHeader(){
      $this->assignHeader('origem',$this->origem);
      $this->assignHeader('nomeOrigem',$this->nomeOrigem);
      $this->assignHeader('charset',$this->charset);
      $this->assignHeader('boundary',$this->boundary);
      $this->assignHeader('extra',''); //future use
    }
    private function gerarResultado(){
      $this->gerarHeader();

      $body = "";
      $body .= "--".$this->boundary.PHP_EOL;
      $body .= 'Content-Transfer-Encoding: 8bit'.PHP_EOL;
      $body .= 'Content-Type: text/html; charset="utf-8"'.PHP_EOL.PHP_EOL;
      $body .= $this->mensagem.PHP_EOL;

      if($this->hasAttachments()){
        $body .= "--".$this->boundary.PHP_EOL;
        $body .= implode("--".$this->boundary.PHP_EOL,$this->gerarAnexos());
      }
      $body .= "--".$this->boundary."--".PHP_EOL;

      return $body;
    }
    private function assignHeader($tag,$valor){
      $this->headers = str_replace('{'.$tag.'}',$valor,$this->headers);
    }
    private function checkEstourouLimite($i,$maximo,$pagina){
      if($i+1 == $maximo) return true;
      if(($i+1) - ($pagina*$this->destinosPorEmail) >= $this->destinosPorEmail) return true;
      return false;
    }
  }

