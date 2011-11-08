<?php
	abstract class Event{
		private $target;
		private $lockTarget = false;
		
		public function setTargetOnce($obj){
			if($this->lockTarget) return;
			$this->target = $obj;
			$this->lockTarget = true;
		}
		abstract public function getName();
		public function getTarget(){
			return $this->target;
		}
	}
?>