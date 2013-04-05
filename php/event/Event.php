<?php
	abstract class Event{
		private $target;
		
		public function setTargetOnce($obj){
			if(!is_null($this->target)) return;
			$this->target = $obj;
		}
		public function getTarget(){
			return $this->target;
		}
		
		abstract public function getName();
	}
?>