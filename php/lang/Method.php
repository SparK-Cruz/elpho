<?php
	class Method{
		private $callback;
		
		public function Method($obj,$chamada=""){
			$this->register($obj,$chamada);
		}
		
		public function register($obj,$chamada=""){
			
			if(is_array($obj) or !$chamada){
				$this->callback = $obj;
				return;
			}
			$this->callback = array($obj,$chamada);
		}
		public function run(){
			return call_user_func_array($this->callback,func_get_args());
		}
		public function __invoke(){
			return call_user_func_array(array($this,"run"),func_get_args());
		}
	}
?>