elpho
=====

Extension Library for PHP OO

### WARNING
This framework is meant for non-html serving php code.
It's structure is dedicated to solve the leaked scope and procedural ancestry of PHP.
This is not a MVC framework, this is only a "C" framework (micro-framework) as it's meant for backend APIs and Services.

### Packaging
As this framework follows the Java way of doing things, we don't use the namespace solution.
Instead we use the good old import, but this time as a function.
The framework maps the folders and classes to defined strings, so you don't have to worry about quotes and slashes.
Just notice the only way you tell a class belongs to a certain package is the folder structure, there's no package declaration.

Here is the HelloWorld.php sample:

	<?php
		require("path/to/elpho/startup.php");
		
		import(php.lang.String);
		
		class HelloWorld{
			public static final function main($args=array()){
				$word = new String("Hello World!");
				print($word);
			}
		}
	?>
First line is only necessary in the file that is exposed to the web.
It's your main class, your entry point, your index or however you call it.
The third line is calling the topLevel function `import`, passing a `classId` string to it (resolves into `"php/lang/String"`).
The sixth line is declaring the entry method. After all the definitions are read, the framework automatically calls the main method.

### System
The system folder contains all the files that make the framework work.
What's new in userland is declared in the file `system/topLevel.php`:

1. `registerMain(file)`
   This method is used when you need to tell elpho that your exposed file is not the main class.
Just pass the main class file name and it will do the dirty work for you.

2. `import(classId)`
   This is the importing function, it will search and include the class definition.

3. `usePack(packageId)`
   This function is a shortcut, it allows you to add a package you already know to the importPath.
   It allows you to use classes by it's package name like `new package\Class()` without having to type the whole thing.  
   Eg:


		<?php
			//we have class "very.long.path.to.the.file.a.MyClass"
			//and "very.long.path.to.the.file.b.MyClass"
			//To use both:
			usePack(very.long.path.to.the.file);
			
			$a = new a\MyClass(); //On the fly dynamic namespace declaration.
			$b = new b\MyClass(); //Sounds great but be warned: it uses eval().
		?>

4. `loadModule(path)`
   This function loads plugins and modules for the framework, as well as adds their path to the includePath and importPath, it also runs their `startup.php` if any.

5. `call(function [, argument...])`
   An alias to `call_user_func`.

6. `callArgs(function , argumentArray)`
   An alias to `call_user_func_array`.

7. `alias(newName , oldName)`
   Creates alias for classes.

8. `named(constructorMethod [, className])`
   Creates named constructors off of methods.
   The method must begin with a single underscore (`_method`), after the class declaration just call `named("method");`.
   Now you may use your named constructor: `new Class_method()`.

9. `checkOverload(type...)`
   This returns true of the list of arguments in the current method matches the types passed to it. Use with caution.

## WORK IN PROGRESS
Yes, it is...
