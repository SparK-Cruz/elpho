<?php
	class Dynamic extends stdClass implements ArrayAccess{
		public function __call($key,$params=array()){
			if(!isset($this->{$key})) throw new Exception("Call to undefined method ".get_class($this)."::".$key."()");
			$subject = $this->{$key};
			$backtrace = debug_backtrace();
			$validKeys = array($key,"call_user_func_array","__call");
			
			$obj = $backtrace[1]["object"];
			
			foreach($backtrace as $index => $jump){
				if(!in_array($jump["function"],$validKeys)) break;
				if(!isset($jump["object"])) continue;
				$obj = $jump["object"];
			}
			
			array_unshift($params,$obj);
			return call_user_func_array($subject,$params);
		}
		
		//Implementing ArrayAccess
		public function offsetExists($offset){
			return isset($this->{$offset});
		}
		public function offsetGet($offset){
			return $this->{$offset};
		}
		public function offsetSet($offset,$value){
			$this->{$offset} = $value;
		}
		public function offsetUnset($offset){
			unset($this->{$offset});
			$this->{$offset} = null;
		}
		public function __toString(){
			return '[object '.get_class($this).']';
		}
	}
?>