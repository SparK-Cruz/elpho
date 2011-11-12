<?php
	class Dynamic extends stdClass implements ArrayAccess{
		
		/**
		 * Calling params
		 * @param string $key
		 * @param array $params
		 * @return void
		 */
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
		
		/**
		 * Implementing ArrayAccess with set
		 * @param mixed $offset
		 * @return boolean
		 */
		public function offsetExists($offset){
			return isset($this->{$offset});
		}
		
		/**
		 * Implementing ArrayAccess with get
		 * @param mixed $offset
		 * @return mixed
		 */
		public function offsetGet($offset){
			return $this->{$offset};
		}
		
		/**
		 * Implementing ArrayAccess with set
		 * @param mixed $offset
		 * @param mixed $value
		 * @return void
		 */
		public function offsetSet($offset,$value){
			$this->{$offset} = $value;
		}
		
		/**
		 * Implementing ArrayAccess with unset
		 * @param mixed $offset
		 * @return void
		 */
		public function offsetUnset($offset){
			unset($this->{$offset});
			$this->{$offset} = null;
		}
		
		/**
		 * toString failsafe magic method
		 * @return string
		 */
		public function toString(){
			return '[object '.get_class($this).']';
		}
		public function __toString(){
			return $this->toString();
		}
	}
?>