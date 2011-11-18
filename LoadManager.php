<?php
	class LoadManager{
		public static function import($root=false,$user=true){
			if(!$root) return;
			$root = str_replace("*",'',$root);
			
			$pathList = self::getIncludePath();
			foreach($pathList as $current){
				$current = str_replace("\\","/",$current)."/";
				$path = $current.$root;
				$file = $path.'.php';
				$base = basename($file);
				
				if(is_file($file)){
					if(ord($base[0]) < ord('A') or ord($base[0]) > ord('Z')) continue; //uppercase check
					self::importFile($root.'.php');
					return;
				}
				
				if(!file_exists($path)) continue;
				if($user) self::registerInUse($root);
				foreach(glob($path."*") as $path){
					$path = str_replace($current,'',$path);
					if(is_dir($current.$path)) $path .= "/";
					if(substr($path,-4) == ".php") $path = substr($path,0,-4);
					self::import($path,false);
				}
			}
		}
		public static function defineFolderMap($targetPath="",$ignoredEntries="?"){
			$includePath = str_replace($ignoredEntries.PATH_SEPARATOR,'',get_include_path());
			$pathList = explode(PATH_SEPARATOR,$includePath);
			
			foreach($pathList as $current){
				$current = str_replace("\\","/",$current)."/";
				
				foreach(glob($current.$targetPath."*") as $path){
					$path = str_replace($current,'',$path);
					
					if(is_dir($current.$path)){
						self::createDefineDir($path,$ignoredEntries);
						continue;
					}
					self::createDefineFile($path);
				}
			}
		}
		private static function createDefineFile($path){
			$name = basename($path,".php");
			if(!defined($name)) define($name,$name);
		}
		private static function createDefineDir($path,$ignoredEntries){
			$chave = basename($path);
			if(!defined($chave)) define($chave,$chave."/");
			
			self::defineFolderMap($path."/",$ignoredEntries);
		}
		public static function getImportTree(){
			$report = $GLOBALS['imports'];
			foreach($report as &$item) $item = array_flip($item);
			return $report;
		}
		public static function getUseTree(){
			$report = $GLOBALS['uses'];
			foreach($report as &$item) $item = array_flip($item);
			return $report;
		}
		private static function startImportTree(){
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			
			$imports = &$GLOBALS['imports'];
			if(!is_array($imports)) $imports = array();
			
			$control = &$imports[$arquivo];
			if(!is_array($control)) $control = array();
		}
		private static function startUseTree(){
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			
			$uses = &$GLOBALS['uses'];
			if(!is_array($uses)) $uses = array();
			
			$useControl = &$uses[$arquivo];
			if(!is_array($useControl)) $useControl = array();
		}
		public static function killImportTree(){
			unset($GLOBALS['imports']);
		}
		public static function killUseTree(){
			unset($GLOBALS['uses']);
		}
		private static function registerInTree($classname){
			self::startImportTree();
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			$imports = &$GLOBALS['imports'];
			$control = &$imports[$arquivo];
			$control[$classname] = count($control);
		}
		private static function importFile($path){
			$name = basename($path,".php");
			
			if(class_exists($name)) return;
			
			self::registerInTree($name);
			require_once($path);
		}
		
		public static function autoload($classe){
			if(strpos($classe,"\\") === false) return;
			if(class_exists($classe)) return;
			
			$valid = array("class","interface");
			$classe = str_replace("\\","/",$classe);
			$includePath = self::getIncludePath();
			
			foreach($includePath as $current){
				$current = str_replace("\\","/",$current)."/";
				
				foreach($valid as $ext){
					$nome = $classe.".".$ext.".php";
					
					if(!file_exists($current.$nome)) continue;
					self::importNamespacedFile($current.$nome,dirname($nome));
					return;
				}
			}
		}
		
		private static function importNamespacedFile($path,$namespace){
			if($namespace == "."){
				self::importFile($path);
				return;
			}
			
			$conteudo = file_get_contents($path);
			$namespace = str_replace("/","\\",$namespace);
			$conteudo = str_replace("<?php", "<?php namespace ".$namespace."; ", $conteudo);
			
			$arquivo = "T".date("YmdHis").round(microtime()).".php";
			file_put_contents($arquivo,$conteudo);
			self::importFile($arquivo);
			unlink($arquivo);
		}
		private static function registerInUse($path){
			self::startUseTree();
			
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			$uses = &$GLOBALS['uses'];
			$control = &$uses[$arquivo];
			
			$path = substr(str_replace("/","\\", $path),0,strlen($path)-1);
			$control[$path] = count($control);
		}
		private static function getCurrentUseList(){
			self::startUseTree();
			
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			$uses = &$GLOBALS['uses'];
			$useControl = &$uses[$arquivo];
			
			return array_flip($useControl);
		}
		private static function getIncludePath(){
			$uses = self::getCurrentUseList();
			$newUses = array();
			
			$includePath = explode(PATH_SEPARATOR,get_include_path());
			foreach($includePath as $caminho){
				foreach($uses as $use){
					$newUses[] = trim($caminho."\\".$use);
				}
			}
			$includePath = array_merge($includePath,$newUses);
			
			return $includePath;
		}
	}
?>