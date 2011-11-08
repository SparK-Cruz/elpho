<?php
	class JavaScript{
		public static function run($codigo){
			print '<script type="text/javascript">'.$codigo.'</script>';
		}
		public static function alert($mensagem){
			JavaScript::run("alert('".addslashes(html_entity_decode($mensagem))."');");
		}
		public static function getUrl($url){
			JavaScript::run("window.location = '".addslashes($url)."';");
		}
		public static function console($acao,$mensagem){
			JavaScript::run("console.".$acao."('".addslashes($mensagem)."');");
		}
		public static function dump($mensagem){
			JavaScript::run("dump('".$mensagem."')");
		}
	}
	//alias
	alias("JS",JavaScript);
?>