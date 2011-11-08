<?php
	import(php.lang.String);
	import(php.extension.YouTubeVideo);
	import(php.extension.VimeoVideo);
	
	abstract class Video{
		public static function create($id){
			$class = self::detectClass($id);
			return new $class($id);
		}
		private static function detectClass($id){
			$url = new String($id);
			if($url->contains("http")){
				if($url->contains("vimeo"))
					return "VimeoVideo";
				return "YouTubeVideo";
			}
			
			switch($url->length()){
				/*case 11:
					return "YouTubeVideo";*/
				case 8:
					return "VimeoVideo";
				default:
					return "YouTubeVideo";
			}
		}
	}
?>