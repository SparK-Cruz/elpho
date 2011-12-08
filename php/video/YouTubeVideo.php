<?php
	import(php.video.Embedable);
	import(php.io.Image);
	
	class YouTubeVideo implements Embedable{
		private $id;
		private $title;
		private $author;
		private $description;
		private $duration;
		private $views;
		private $favorite;
		private $publishDate;
		private $publishTime;
		
		const IMAGE_URL = 'http://img.youtube.com/vi/%s/%s.jpg';
		
		//constructor
		public function YouTubeVideo($id=""){
			if($id == "") return;
			
			if(strpos($id,"v=") !== false){
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
			$anchor = strpos($url,"v=")+2;
			$end = strpos($url,'&');
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
			return $this->author;
		}
		public function getDescription(){
			return $this->description;
		}
		public function getPublishTime(){
			return $this->publishTime;
		}
		public function getPublishDate(){
			return $this->publishDate;
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
			return $this->favorite;
		}
		public function getViews(){
			return $this->views;
		}
		public function getUrl(){
			return 'http://www.youtube.com/watch?v='.$this->id;
		}
		public function getApi(){
			return 'http://gdata.youtube.com/feeds/api/videos/'.$this->id;
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
			return String::format(self::IMAGE_URL,$this->id,"default");
		}
		public function getLinkImageL(){
			return String::format(self::IMAGE_URL,$this->id,0);
		}
		public function getLinkImageM(){
			return String::format(self::IMAGE_URL,$this->id,1);
		}
		public function getLinkImageS(){
			return String::format(self::IMAGE_URL,$this->id,2);
		}
		public function getEmbedCode($width=586,$height=360){
			return '<iframe title="YouTube video player" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$this->id.'?rel=0" frameborder="0" allowfullscreen></iframe>';
		}
		public function getPlayer(){
			return 'http://www.youtube.com/v/'.$this->id;
		}
		
		//extra
		private function grabInfo(){
			$handler = new DOMDocument();
			$result = @$handler->load($this->getApi());
			
			if(!$result) throw new Exception("Link de Vídeo invalido.");
			
			$this->title = $handler->getElementsByTagName("title")->item(0)->nodeValue;
			$this->author = $handler->getElementsByTagName("author")->item(0)->getElementsByTagName("name")->item(0)->nodeValue;
			$this->description = $handler->getElementsByTagName("content")->item(0)->nodeValue;
			$this->duration = $handler->getElementsByTagName("duration")->item(0)->getAttribute("seconds");
			$stats = $handler->getElementsByTagName("statistics")->item(0);
			$this->views = $stats->getAttribute("viewCount");
			$this->favorite = $stats->getAttribute("favoriteCount");
			$publishTimestamp = $handler->getElementsByTagName("published")->item(0)->nodeValue;
			$this->publishDate = substr($publishTimestamp,0,10);
			$this->publishTime = substr($publishTimestamp,strpos($publishTimestamp,"T")+1,8);
		}
	}
?>
