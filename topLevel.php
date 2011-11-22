<?php
	function registerMain($filename=null){
		Starter::registerMain($filename);
	}
	function import($root=false){
		LoadManager::import($root);
	}
	function call($callback,$args=null,$_=null){
		$args = func_get_args();
		array_shift($args);
		return call_user_func_array($callback,$args);
	}
	function callArgs($callback,$args){
		return call_user_func_array($callback,$args);
	}
	function alias($new,$old){
		eval("class ".$new." extends ".$old."{}");
	}
	function named($constructor,$class){
		$name = $class."_".$constructor;
		eval("class ".$name." extends ".$class."{ public function ".$name."(){ call_user_func_array(array(parent,".$name."), func_get_args()); } }");
	}
	function __autoload($classe){
		LoadManager::autoload($classe);
	}
	
	function checkOverload($arguments,$type,$_=null){
		$types = func_get_args();
		$arguments = array_shift($types);
		
		$i = 0;
		foreach($types as $type){
			$argument = $arguments[$i];
			
			if(is_object($argument)){
				if(!is_a($argument,$type)) return false;
				continue;
			}
			
			$type = ($type=="float"?"double":$type);
			if(gettype($argument) !== $type) return false;
			$i++;
		}
		return true;
	}
?>
