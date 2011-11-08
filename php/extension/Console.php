<?php
	import(php.extension.JavaScript);
	
	class Console{
		public static function log($mensagem){
			JavaScript::console("log",$mensagem);
		}
		public static function debug($mensagem){
			JavaScript::console("debug",$mensagem);
		}
		public static function info($mensagem){
			JavaScript::console("info",$mensagem);
		}
		public static function warn($mensagem){
			JavaScript::console("warn",$mensagem);
		}
		public static function error($mensagem){
			JavaScript::console("error",$mensagem);
		}
		public static function dir($objeto){
			JavaScript::console("dir",$objeto);
		}
	}
?>