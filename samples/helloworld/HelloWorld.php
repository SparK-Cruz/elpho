<?php
	require("../../startup.php");
	
	import(php.lang.String);
	
	class HelloWorld{
		public static final function main($args=array()){
			$word = new String("Hello World!");
			print($word);
		}
	}
