<?php
	import(php.extension.Embedable);
	import(php.lang.Image);
	
	class VimeoVideo implements Embedable{
		private $id;
		private $title;
		private $userName;
		private $description;
		private $duration;
		private $plays;
		private $likes;
		private $uploadDate;
		private $uploadTime;
		
		private $imageSmall;
		private $imageMedium;
		private $imageLarge;
		
		//constructor
		public function VimeoVideo($id=""){
			if($id == "") return;
			if(strpos($id,".com/") !== false){
				$this->setUrl($id);
				return;
			}
			
			$this->setId($id);
		}
		
		//set
		public function setId($id){
			$this->id = $id;
			$this->grabInfo($id);
		}
		public function setUrl($url){
			$anchor = strpos($url,".com/")+5;
			$end = strpos($url,'/',$anchor+1);
			if($end === false) $end = strlen($url);
			$end -= $anchor;
			$id = substr($url,$anchor,$end);
			
			$this->setId($id);
		}
		
		//get
		public function getId(){
			return $this->id;
		}
		public function getTitle(){
			return $this->title;
		}
		public function getAuthor(){
			return $this->userName;
		}
		public function getDescription(){
			return $this->description;
		}
		public function getPublishTime(){
			return $this->uploadTime;
		}
		public function getPublishDate(){
			return $this->uploadDate;
		}
		public function getTime(){
			return $this->duration;
		}
		public function getTimeString(){
			$time = $this->duration;
			$seconds = floor($time % 60);
			$minutes = floor(($time/60) % 60);
			$hours = floor($time/60/60);
			
			$seconds = $seconds < 10 ? "0".$seconds : $seconds;
			$minutes = $minutes < 10 ? "0".$minutes : $minutes;
			$hours = $hours < 10 ? "0".$hours : $hours;
			
			return $hours.":".$minutes.":".$seconds;
		}
		public function getFavorite(){
			return $this->likes;
		}
		public function getViews(){
			return $this->plays;
		}
		public function getUrl(){
			return 'http://vimeo.com/'.$this->id;
		}
		public function getApi(){
			return 'http://vimeo.com/api/v2/video/'.$this->id.'.xml';
		}
		public function getImage(){
			return $this->getImageL();
		}
		public function getImageL(){
			return new Imagem($this->getLinkImageL());
		}
		public function getImageM(){
			return new Imagem($this->getLinkImageM());
		}
		public function getImageS(){
			return new Imagem($this->getLinkImageS());
		}
		public function getLinkImage(){
			return $this->getLinkImageL();
		}
		public function getLinkImageL(){
			return $this->imageLarge;
		}
		public function getLinkImageM(){
			return $this->imageMedium;
		}
		public function getLinkImageS(){
			return $this->imageSmall;
		}
		public function getEmbedCode(){
			return '<iframe src="http://player.vimeo.com/video/'.$this->id.'?portrait=0" width="586" height="360" frameborder="0"></iframe>';
		}
		public function getPlayer(){
			return 'http://player.vimeo.com/video/'.$this->id;
		}
		
		//extra
		private function grabInfo(){
			$handler = new DOMDocument();
			$result = @$handler->load($this->getApi());
			
			if(!$result) throw new Exception("Link de Vídeo invalido.");
			
			$this->title = $handler->getElementsByTagName("title")->item(0)->nodeValue;
			$this->userName = $handler->getElementsByTagName("user_name")->item(0)->nodeValue;
			$this->description = $handler->getElementsByTagName("description")->item(0)->nodeValue;
			$this->duration = $handler->getElementsByTagName("duration")->item(0)->nodeValue;
			$this->plays = $handler->getElementsByTagName("stats_number_of_plays")->item(0)->nodeValue;
			$this->likes = $handler->getElementsByTagName("stats_number_of_likes")->item(0)->nodeValue;
			$this->imageSmall = $handler->getElementsByTagName("thumbnail_small")->item(0)->nodeValue;
			$this->imageMedium = $handler->getElementsByTagName("thumbnail_medium")->item(0)->nodeValue;
			$this->imageLarge = $handler->getElementsByTagName("thumbnail_large")->item(0)->nodeValue;
			$publishTimestamp = explode(" ",$handler->getElementsByTagName("upload_date")->item(0)->nodeValue);
			$this->uploadDate = $publishTimestamp[0];
			$this->uploadTime = $publishTimestamp[1];
		}
	}
?>