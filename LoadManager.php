<?php
	final class LoadManager extends StaticType{
		private static $ignoredEntries="?";
		
		public static function loadModule($path,$runStartup=true){
			$entries = str_replace(".".PATH_SEPARATOR,'',get_include_path());
			set_include_path(".".PATH_SEPARATOR.$path.PATH_SEPARATOR.$entries);
			self::defineFolderMap("");
			if($runStartup){
				$file = $path."/startup.php";
				if(file_exists($file)) include($file);
			}
		}
		public static function import($root=false,$level=0){
			if(!$root) return;
			$root = str_replace("*",'',str_replace('.php','',$root));
			
			$pathList = self::getIncludePath();
			foreach($pathList as $current){
				$current = str_replace("\\","/",$current)."/";
				$path = $current.$root;
				$file = $path.'.php';
				$base = basename($file);
				
				if(is_file($file)){
					if(!preg_match('/^[A-Z]/',$base)) continue; //uppercase check
					self::importFile($file);
					return;
				}
				
				if(!file_exists($path)) continue;
				if(!$level) self::registerInUse($root);
				foreach(self::listFolder($path) as $part){
					$full = $root.$part;
					if(is_dir($current.$full)) $full .= "/";
					if(substr($full,-4) == ".php") $full = substr($full,0,-4);
					self::import($full,$level+1);
				}
			}
		}
		public static function ignorePaths($ignoredEntries="?"){
			self::$ignoredEntries = $ignoredEntries;
		}
		public static function defineFolderMap($targetPath=""){
			//safety check
			$proceed = false;
			$trace = debug_backtrace();
			if(isset($trace[1]))
			switch($trace[1]["class"]){
				case "Starter":
				case "LoadManager":
					$proceed = true;
				break;
			}
			if(!$proceed) throw new Exception("This method cannot be called in userland!");
			
			$includePath = str_replace(PATH_SEPARATOR.self::$ignoredEntries,'',get_include_path());
			$pathList = explode(PATH_SEPARATOR,$includePath);
			
			foreach($pathList as $current){
				$current = str_replace("\\","/",$current)."/";
				
				foreach(self::listFolder($current.$targetPath) as $path){
					$full = $targetPath.$path;
					if(is_dir($current.$full)){
						self::createDefineDir($full);
						continue;
					}
					self::createDefineFile($path);
				}
			}
		}
		private static function listFolder($pattern){
			$list = array();
			
			call_user_func(function() use($pattern,&$list){
				if(!is_dir($pattern)) return;
				
				$dir = opendir($pattern);
				while($file = readdir($dir)){
					if($file[0] == ".") continue;
					$list[] = $file;
				}
				closedir($dir);
			});
			
			return $list;
		}
		private static function createDefineFile($path){
			$name = basename($path,".php");
			if(!defined($name)) define($name,$name);
		}
		private static function createDefineDir($path){
			$chave = basename($path);
			if(!defined($chave)) define($chave,$chave."/");
			self::defineFolderMap($path."/");
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
			
			$classe = str_replace("\\","/",$classe);
			$includePath = self::getIncludePath();
			
			foreach($includePath as $current){
				$current = str_replace("\\","/",$current)."/";
				
				$nome = $classe.".php";
				if(!file_exists($current.$nome)) continue;
				self::importNamespacedFile($current.$nome,dirname($nome));
				return;
			}
		}
		
		private static function importNamespacedFile($path,$namespace){
			if($namespace == "."){
				self::importFile($path);
				return;
			}
			$classe = basename($path,".php");
			
			$conteudo = file_get_contents($path);
			$imports = self::captureImports($conteudo);
			
			$globalClasses = get_declared_classes();
			$globalClasses += get_declared_interfaces();
			
			foreach($imports as $import){
				$import = str_replace(".","/",$import);
				if(strpos("/".$import,"/".$namespace."/") !== false) continue;
				$globalClasses[] = basename($import);
			}
			
			$namespace = str_replace("/","\\",$namespace);
			
			$conteudo = str_replace('<?php','<?php namespace '.$namespace.';',$conteudo);
			$conteudo = str_replace('function '.$classe,'function __construct(){ call_user_func_array(array($this,'.$classe.'),func_get_args()); }'."\n".'function '.$classe,$conteudo);
			
			foreach($globalClasses as $globalClass){
				if($globalClass == $classe) continue;
				$conteudo = str_replace(" ".$globalClass," \\".$globalClass,$conteudo);
			}
			
			$arquivo = "T".date("His").$classe.round(microtime(false)*1000).".php";
			file_put_contents($arquivo,$conteudo);
			try{
				self::importFile($arquivo);
			}catch(Exception $e){
				unlink($arquivo);
				throw $e;
			}
			unlink($arquivo);
		}
		private static function captureImports($fileContents){
			$matches = array();
			preg_match_all('/import ?\([^;]+\)/', $fileContents, $matches);
			$matches = array_map(function($item){
				return preg_replace('/(import ?\()|(\))/', '', $item);
			},$matches[0]);
			return $matches;
		}
		public static function registerInUse($path){
			self::startUseTree();
			
			$arquivo = $_SERVER["SCRIPT_FILENAME"];
			$uses = &$GLOBALS['uses'];
			$control = &$uses[$arquivo];
			
			$path = str_replace("/","\\", $path);
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
			
			$includePath = explode(PATH_SEPARATOR,str_replace(PATH_SEPARATOR.self::$ignoredEntries,'',get_include_path()));
			
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
