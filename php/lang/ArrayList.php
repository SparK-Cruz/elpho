<?php
	import(php.lang.String);
	
	class ArrayList implements Serializable, ArrayAccess, IteratorAggregate{
		protected $elements;
		
		//implementing IteratorAggregate
		public function getIterator(){
			return new ArrayIterator($this->elements);
		}
		
		//implementing ArrayAccess
		public function offsetExists($offset){
			if(!is_numeric($offset) or !isset($this->elements[$offset])) return false;
			return true;
		}
		public function offsetGet($offset){
			return $this->elements[$offset];
		}
		public function offsetSet($offset,$value){
			if(!is_numeric($offset)) return;
			if(!$offset and $offset !== 0) return;
			$this->elements[$offset] = $value;
		}
		public function offsetUnset($offset){
			unset($this->elements[$offset]);
		}
		
		public function ArrayList($elements=null,$_=null){
			$this->elements = array();
			if($elements === null) return;
			$this->elements = func_get_args();
		}
		
		public function merge($array){
			if(is_object($array)) $array = $array->toPrimitive();
			return ArrayList::create(array_merge($this->elements,$array));
		}
		
		public static function create($array){
			if(is_a($array,ArrayList)) $array = $array->toPrimitive();
			$list = new ArrayList();
			
			if(count($array))
				call_user_func_array(array($list,"push"), $array);
			
			return $list;
		}
		
		public function indexOf($element,$offset=0){
			foreach($this->elements as $key => $target){
				if($key < $offset or $element != $target) continue;
				return $key;
			}
		}
		public function join($separator=""){
			return new String(implode($separator,$this->elements));
		}
		public function lastIndexOf($element,$offset=null){
			$lastIndex = $this->getLastKey();
			if($offset === null) $offset = $lastIndex;
			$offset = $lastIndex - $offset;
			$reverse = $this-reverse();
			$index = $reverse->indexOf($element,$offset);
			$index = $lastIndex - $index;
			return $index;
		}
		public function map($callback){
			return self::create(array_map($callback,$this->elements));
		}
		public function pop(){
			$lastKey = $this->getLastKey();
			$obj = $this->elements[$lastKey];
			unset($this->elements[$lastKey]);
			$this->flushKeys();
			return $obj;
		}
		public function push($elements,$_=null){
			$this->elements = array_merge($this->elements,func_get_args());
			return $this->length();
		}
		public function reverse(){
			return self::create(array_reverse($this->elements));
		}
		public function shift(){
			$firstKey = $this->getFirstKey();
			echo $this->elements[0];
			$obj = $this->elements[$firstKey];
			unset($this->elements[$firstKey]);
			$this->flushKeys();
			return $obj;
		}
		public function unshift($elements,$_=null){
			$this->elements = array_merge(func_get_args(),$this->elements);
			return $this->length();
		}
		public function slice($start,$length=null){
			if($length === null) $length = $this->length()-$start;
			return self::create(array_slice($this->elements,$start,$length));
		}
		public function splice($start,$length=null,$values=null,$_=null){
			$inserted = array();
			$new = array();
			$return = array();
			
			if($length === null) $length = $this->length()-$start;
			if($values !== null) $inserted = array_slice(func_get_args(),2);
			
			$return = array_slice($this->elements,$start,$length);
			
			foreach($this->elements as $key => $element){
				if($key >= $start) continue;
				$new[] = $element;
			}
			$new = array_merge($new,$inserted);
			foreach($this->elements as $key => $element){
				if($key < $start+$length) continue;
				$new[] = $element;
			}
			
			$this->elements = $new;
			$this->flushKeys();
			
			return self::create($return);
		}
		public function length(){
			return count($this->elements);
		}
		
		public function contains($value){
			return in_array($value,$this->elements);
		}
		
		public function unique(){
			return self::create(array_unique($this->elements));
		}
		
		public function set($index,$value){
			if(!is_numeric($index)) return;
			$this->elements[$index] = $value;
		}
		public function get($index){
			return $this->relements[$index];
		}
		
		private function getLastKey(){
			$lastKey = false;
			foreach($this->elements as $key => $element)
				$lastKey = $key;
			
			return $lastKey;
		}
		private function getFirstKey(){
			$firstKey = 0;
			foreach($this->elements as $key => $element){
				$firstKey = $key;
				break;
			}
			return $firstKey;
		}
		private function flushKeys(){
			$new = array();
			foreach($this->elements as $value){
				$new[] = $value;
			}
			$this->elements = $new;
		}
		
		public function toPrimitive(){
			return $this->elements;
		}
		public function toString(){
			return $this->join(',')->toString();
		}
		
		public function serialize(){
			return serialize($this->elements);
		}
		public function unserialize($data){
			$this->elements = unserialize($data);
		}
		
		public function __toString(){
			return $this->toString();
		}
	}
?>