<?php
	import(php.lang.File);
	
	class Image extends File{
		protected $width;
		protected $height;
		
		//constructor
		public function Image($nome=""){
			parent::File($nome);
			if($nome){
				$tamanho = getimagesize($nome);
				$this->width = $tamanho[0];
				$this->height = $tamanho[1];
				$this->setType($tamanho["mime"]);
			}
		}
		
		//set
		public function setExtension($ext){
			$oldType = $this->tipo;
			parent::setExtension($ext);
			$this->atualizarTipo($oldType);
		}
		public function setType($tipo){
			$oldType = $this->tipo;
			parent::setType($tipo);
			$this->atualizarTipo($oldType);
		}
		public function setContent($conteudo){
			$this->setResource(imagecreatefromstring($conteudo));
		}
		public function setResource($res){
			$conteudo = "";
			ob_start();
			switch($this->extensao){
				case "jpg":
					imagejpeg($res,null,90);
				break;
				case "png":
					imagepng($res);
				break;
				case "gif":
					imagegif($res);
				break;
				case "bmp":
					imagewbmp($res);
				break;
			}
			$conteudo = ob_get_contents();
			ob_end_clean();
			
			$this->width = imagesx($res);
			$this->height = imagesy($res);
			
			$this->conteudo = $conteudo;
		}
		
		//get
		public function getWidth(){
			return $this->width;
		}
		public function getHeight(){
			return $this->height;
		}
		public function getInfo(){
			$info = parent::getInfo();
			return array_merge($info,array($this->getWidth(),$this->getHeight()));
		}
		public function getResource(){
			return imagecreatefromstring($this->conteudo);
		}
		public function getCopy(){
			$copy = new Image();
			$copy->setContent($this->getContent());
			return $copy;
		}
		
		//actions
		public function applyWatermark($marca,$transparencia,$position="center"){
			$image = $this->criarImagem($this->width,$this->height);
			
			$markResource = $marca->getResource();
			$local = $this->getResource();
			
			$nova = $this->criarImagem($this->width,$this->height);
			$background = imagecolorat($markResource,0,0);
			imagefill($nova,0,0,$background);
			imagecolortransparent($nova,$background);
			
			$size = array($this->width,$this->height);
			$markSize = array($marca->getWidth(),$marca->getHeight());
			
			list($x,$y) = $this->calcularPosicao($size[0],$size[1],$markSize[0],$markSize[1],$position);
			
			imagecopyresampled($image,$local,0,0,0,0,$size[0],$size[1],$size[0],$size[1]);
			imagecopyresampled($nova,$local,0,0,0,0,$size[0],$size[1],$size[0],$size[1]);
			imagecopyresampled($nova,$markResource,$x,$y,0,0,$markSize[0],$markSize[1],$markSize[0],$markSize[1]);
			
			imagecopymerge($image,$nova,0,0,0,0,$size[0],$size[1],$transparencia);
			
			$retorno = $this->getCopy();
			$retorno->setType($this->getType());
			$retorno->setResource($image);
			return $retorno;
		}
		public function resize($width=0,$height=0){
			if($width == 0) $width = $this->width;
			if($height == 0) $height = $this->height;
			$nova = $this->criarImagem($width,$height);
			$arquivo = $this->getResource();
			
			imagecopyresampled($nova,$arquivo,0,0,0,0,$width,$height,$this->width,$this->height);
			
			$this->width = $width;
			$this->height = $height;
			
			$retorno = $this->getCopy();
			$retorno->setType($this->getType());
			$retorno->setResource($nova);
			return $retorno;
		}
		public function resizeCanvas($width=0,$height=0,$position="center"){
			if($width == 0) $width = $this->width;
			if($height == 0) $height = $this->height;
			
			list($x,$y) = $this->calcularPosicao($width,$height,$this->width,$this->height,$position);
			
			$nova = $this->criarImagem($width, $height);
			$background = imagecolorallocatealpha($nova,0,0,0,127);
			imagefill($nova,0,0,$background);
			
			$arquivo = $this->getResource();
			
			imagecopyresampled($nova,$arquivo,$x,$y,0,0,$this->width,$this->height,$this->width,$this->height);
			
			$this->width = $width;
			$this->height = $height;
			
			$retorno = $this->getCopy();
			$retorno->setType($this->getType());
			$retorno->setResource($nova);
			return $retorno;
		}
		private function calcularPosicao($stageWidth,$stageHeight,$width,$height,$position){
			$coords[0] = ($stageWidth/2)-($width/2);
			$coords[1] = ($stageHeight/2)-($height/2);
			
			if($position == "center") return $coords;
			
			if($position == "top" or $position == "bottom") $position .= "-center";
			if($position == "left" or $position == "right") $position = "center-".$position;
			
			$position = explode("-",$position,2);
			
			if($position[0] == "top") $coords[1] = 0;
			if($position[1] == "left") $coords[0] = 0;
			if($position[0] == "bottom") $coords[1] = $stageHeight-$height;
			if($position[1] == "right") $coords[0] = $stageWidth-$width;
			return $coords;
		}
		public function crop($x,$y,$width=0,$height=0){
			if($width == 0) $width = $this->width;
			if($height == 0) $height = $this->height;
			$nova = $this->criarImagem($width,$height);
			$arquivo = $this->getResource();
			
			imagecopyresampled($nova,$arquivo,0,0,$x,$y,$this->width,$this->height,$this->width,$this->height);
			
			$this->width = $width;
			$this->height = $height;
			
			$retorno = $this->getCopy();
			$retorno->setType($this->getType());
			$retorno->setResource($nova);
			return $retorno;
		}
		private function criarImagem($width,$height){
			$nova = imagecreatetruecolor($width,$height);
			imagealphablending($nova, false);
			imagesavealpha($nova, true);
			$fundo = imagecolorallocatealpha($nova,0,0,0,127);
			imagefill($nova,0,0,$fundo);
			
			return $nova;
		}
		private function atualizarTipo($oldType){
			if(!$this->getSize()) return;
			if($this->tipo == $oldType) return;
			$res = $this->getResource();
			$this->setResource($res);
		}
	}
?>