<?php

	final class LoadManager extends StaticType{
		private static $ELPHO_PATH = "";
		private static $ignoredEntries = "?";
		private static $allowLoad = true;

		private static function addIncludePath($path){
			$entries = str_replace(".".PATH_SEPARATOR,'',get_include_path());
			set_include_path(".".PATH_SEPARATOR.$path.PATH_SEPARATOR.$entries);
		}
		public static function loadElphoPath($path,$ignored="?"){
			if(!self::$allowLoad) return;

			if(!self::$ELPHO_PATH){
				self::$ELPHO_PATH = $path;
			}

			self::ignorePaths($ignored);
			self::addIncludePath($path);
			self::mapPackages();
			self::$allowLoad = false;
		}
		public static function loadModule($path){
			self::$allowLoad = true;
			self::loadElphoPath($path);
			$file = $path."/startup.php";
			if(file_exists($file)) include($file);
		}

		public static function import($id=false){
			require_once("system/load/ClassNotFoundException.php");
			require_once("system/load/PackageNotFoundException.php");

			$isPackage = !preg_match('/^[A-Z]/',basename($id));

			if($isPackage){
				self::importPackage($id);
				return;
			}
			
			$path = self::locateClass($id);
			if(!$path)
				throw new ClassNotFoundException($id);

			self::importFile($path);
		}

		private static function locateClass($classId=false){
			//Basic parameter check
			if(!$classId) return;

			//Uppercase check
			if(!preg_match('/^[A-Z]/',basename($classId))) return;

			$filename = $classId.".php";

			$includePath = self::getIncludePath();
			foreach($includePath as $root){
				if(!is_file(realpath($root."/".$filename))) continue;
				return $filename; //Found it!
			}
		}

		private static function importPackage($packageId){
			require_once("system/load/PackageNotFoundException.php");

			self::registerFolder($packageId);
			$root = null;
			
			foreach(self::getIncludePath() as $base){
				$path = $base."/".$packageId;
				if(!file_exists($path))
					continue;
				
				$root = $path;
				break;
			}
			
			if($root == null)
				throw new PackageNotFoundException($packageId);

			foreach(self::listFolder($root) as $part)
				self::import($packageId.basename($part,".php"));
		}
		private static function importFile($path){
			$className = basename($path,".php");
			if(class_exists($className,false)) return;

			self::registerInTree($className);
			require_once($path);
		}

		public static function ignorePaths($ignoredEntries="?"){
			if($ignoredEntries == "?") return;
			self::$ignoredEntries = $ignoredEntries;
		}
		private static function mapPackages($targetPath=""){
			$includePath = str_replace(PATH_SEPARATOR.self::$ignoredEntries,'',get_include_path());
			$pathList = explode(PATH_SEPARATOR,$includePath);

			foreach($pathList as $current){
				$current = str_replace("\\","/",$current)."/";

				foreach(self::listFolder($current.$targetPath) as $path){
					$full = $targetPath.$path;
					if(is_file($current.$full)){
						self::createFileDefine($path);
						continue;
					}
					self::createFolderDefine($full);
					self::mapPackages($full."/");
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
		private static function createFileDefine($path){
			$name = basename($path,".php");
			if(!defined($name)) define($name,$name);
		}
		private static function createFolderDefine($path){
			$chave = basename($path);
			if(!defined($chave)) define($chave,$chave."/");
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

		public static function autoload($classe){
			if(strpos($classe,"\\") === false) return import($classe);
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

			$arquivo = tempnam(sys_get_temp_dir(), $classe);
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
		public static function registerFolder($path){
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

			$includePath = explode(PATH_SEPARATOR,str_replace(self::$ignoredEntries,'.'.PATH_SEPARATOR,get_include_path().PATH_SEPARATOR."./elpho/"));

			foreach($includePath as $caminho){
				foreach($uses as $use){
					$newUses[] = trim($caminho."\\".$use);
				}
			}
			$includePath = array_merge($includePath,$newUses);

			return $includePath;
		}
	}
