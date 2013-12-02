<?php
	class PackageNotFoundException extends Exception{
		public function __construct($packageId){
			parent::__construct("ELPHO: Package '".$packageId."' not found, check your working directory and class_path.");
		}
	}