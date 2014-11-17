![alt text][logo]

elpho
=====

Extension Library for PHP OO

### WARNING
The core framework is meant for non-html serving php code.
It's structure is dedicated to solve the leaked scope and procedural ancestry of PHP.
This is not a MVC framework, but it has an built-in MVC module you can use.
The core is a language extension and even tough we try to fix some stuff, it's still PHP.

### Packaging
This framework implements packages instead of namespaces.<br/>
The packaging system uses commands like `import` and `usePack` as well as seamless dynamic namespacing to solve class name clashes.<br/>
The framework maps the folders and files to *defined strings* for the `import` command, don't use quotes and slashes on it.<br/>
Just notice the only way you tell a class belongs to a certain package is the folder structure, there's no package declaration like in Java.

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
First line is only necessary in the requested file and once per request. All files exposed to the web should contain it.
The third line is calling the topLevel function `import`, passing a `classId` string to it (notice the lack of slashes and quotes).
The sixth line is declaring the entry method. After all the definitions are read, the framework automatically calls the main method of the entry class.

### System
The system folder contains all the framework core files.
Userland functions are declared in the file `system/topLevel.php`:

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

4. `loadExtension(path)`
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
   This returns true if the list of arguments in the current method matches the types passed to it. Use with caution.

## WORK IN PROGRESS
This framework is a work in progress and always will be.
Remember, doesn't matter how good a framework looks and feels, it's still PHP.

Mail me at roger.cruz(at)ateliware.com.br if you want more information.

[logo]: https://github.com/SparK-Cruz/elpho/raw/master/src/logo.png
