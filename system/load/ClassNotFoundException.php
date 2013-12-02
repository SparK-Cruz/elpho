<?php
	class ClassNotFoundException extends Exception{
		public function __construct($classId){
			parent::__construct("ELPHO: Class '".$classId."' not found, are you missing an import directive?");
		}
	}