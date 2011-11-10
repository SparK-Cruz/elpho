<?php
	class File{
		protected $nome = "";
		protected $conteudo = "";
		protected $tipo = "";
		protected $extensao = "";
		
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
		public function setContent($conteudo){
			$this->conteudo = $conteudo;
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
		public function getContent(){
			return new String($this->conteudo);
		}
		public function exists(){
			return file_exists($this->nome);
		}
		public function getSize(){
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
		public function getCopy(){
			$copy = new File();
			$copy->setContent($this->getContent());
			return $copy;
		}
		
		//actions
		protected function ler(){
			$this->conteudo = file_get_contents($this->nome);
		}
		public function erase(){
			$this->setContent('');
			if($this->exists()) $this->save();
		}
		public function write($texto){
			$this->conteudo .= $texto;
		}
		public function save($nome=null){
			$nome = $nome?:$this->nome;
			$arquivo = fopen($nome,'wb');
			fwrite($arquivo,$this->getContent());
			fclose($arquivo);
			chmod($this->nome,0777);
		}
		public function delete(){
			$this->erase();
			if($this->exists()) unlink($this->nome);
		}
		
		//extra
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
				default:
					$ext = $tipo;
				break;
			}
			return $ext;
		}
	}
?>