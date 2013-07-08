<?php
	import(php.event.EventHandler);
	import(EventHelper);
	
	class DispatcherHelper extends EventHandler{
		public function DispatcherHelper(){}
		
		public function run(){
			$this->dispatchEvent(new EventHelper());
		}
		
		public function toString(){
			return "[object DispatcherHelper]";
		}
		public function __toString(){
			return $this->toString();
		}
	}
