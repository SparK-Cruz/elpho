elpho
=====

Extension Library for PHP OO

Warning: This framework is meant for non-html serving php code.
If you are familiar with Java, be welcome.

	<?php
		//HelloWorld.php
		require("../elpho/startup.php");
		
		import(php.lang.String);
		
		class HelloWorld{
			public static function main($args=array()){
				$word = new String("Hello World!");
				print($word);
			}
		}
