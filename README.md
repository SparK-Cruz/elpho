![alt text][logo]
Extension Library for PHp Object-orientation
============================================

### WARNING
The core framework is meant for non-html serving php code.
It's structure is dedicated to solve the leaked scope and procedural ancestry of PHP.
This is not a MVC framework, but it has a built-in MVC module you can use.
The core is a language extension and even tough we try to fix some stuff, it's still PHP.

The framework uses classes as a means for isolation of files.
Here is the HelloWorld.php example:

```
<?php
   //The framework
   require("path/to/elpho/startup.php");

   //The class name is same as file without ".php"
   class HelloWorld{
      //Entry method (d'JAVu)
      public static final function main($args=array()){
         //Wrapper class with lots of functions
         //Not really useful here
         $word = new String("Hello World!");

         //It calls toString() using PHP magic methods
         print($word);
      }
   }
?>
```

### System
The system folder contains all the framework core files.
Userland functions are declared in the file `system/topLevel.php`, they are:

1. `registerMain(file)`
This method is used when you need to tell elpho that your exposed file is not the main class.
Just pass the main class filename and it will call it when it finishes loading your app.

2. `loadExtension(path)`
This function loads plugins and modules for the framework by adding their path to the include_path, it also runs their `startup.php` if any.

3. `call(function [, argument...])`
An alias to `call_user_func`.

4. `callArgs(function , argumentArray)`
An alias to `call_user_func_array`.

5. `alias(newName , oldName)`
Creates alias for classes. Does **not** unload the aliased class and does **not** override existing classes.

6. `named(constructorMethod [, className])`
Creates named constructors from methods.
Any method starting with a single underscore (`_method`) can be used. After the class declaration add `named("method");` without underscore.
Now you may use your named constructor. ex: Class `ArrayList` has method `_from` so it has both constructors `new ArrayList([el0] [, el1]...)` and `new ArrayList_from(array)`.

7. `matchTypes(type [, type]...)`
Returns true if the list of arguments in the **current function** matches the types passed to it.

## WORK IN PROGRESS
This framework is a work in progress and always will be.
Remember, doesn't matter how good a framework looks and feels, it's still PHP.

Mail me at spark.crz(at)gmail.com if you need to talk.

[logo]: https://raw.githubusercontent.com/SparK-Cruz/elpho/master/logo.png
