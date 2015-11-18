![alt text][logo]
Extension Library for PHp Object-orientation
============================================

Your non-invasive, lightweight, fast, dev-tool framework

### WARNING
The framework core is meant for non-html-serving php code.
It's structure is dedicated to solve the leaked scope and procedural ancestry of PHP.
Although we do have a built-in MVC module, this is not a MVC framework.

The core is a language extension and even tough we try to fix some stuff, it's still PHP.

## Hello Core

Here is the very verbose HelloWorld.php example:

```php
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

## Hello MVC

Here is the MVC module "Hello World":

```php
<?php
   //The framework
   require("path/to/elpho/startup.php");

   requireDirOnce("mvc");

   //The entry class
   class Index{
      public static final function main($args=array()){
         //Router
         $router = Router::getInstance(__DIR__);

         $router->map(array("", "home"), array("Home", "index"));
      }
   }
?>
```

```php
<?php
   //Home controller
   class Home extends Controller{
      public static function index($args){
         //Ideally use the mvc.View class
         $view = new View("template.html.php");
         $view->myMessageAttribute = "Hello World!";
         $view->render();
      }
   }
?>
```

```php
<!-- template.html.php -->
<!DOCTYPE html>
<html>
   <body>
      <p><?=$viewbag->myMessageAttribute?></p>
   </body>
</html>
```

```
   #.htaccess
   RewriteEngine On
   RewriteRule (.*) Index.php [QSD,L]
```

## System
The system folder contains all the framework core files.
Userland functions are declared in the file `system/topLevel.php`, they are:

1. `registerMain(file)`
This method is used when you need to tell elpho that your exposed file is not the main class.
Just pass the main class filename and it will call it when it finishes loading your app.

2. `loadExtension(path)`
This function loads plugins and modules for the framework by adding their path to the include_path, it also runs their `startup.php` if any.

3. `requireDirOnce(path)`
Recursive require files from the directory tree.

4. `call(function [, argument...])`
An alias to `call_user_func`.

5. `callArgs(function , argumentArray)`
An alias to `call_user_func_array`.

6. `alias(newName , oldName)`
Creates alias for classes. Does **not** unload the aliased class and does **not** override existing classes.

7. `named(constructorMethod [, className])`
Creates named constructors from methods.
Any method starting with a single underscore (`_method`) can be used. After the class declaration add `named("method");` without underscore.
Now you may use your named constructor. ex: Class `ArrayList` has method `_from` so it has both constructors `new ArrayList([el0] [, el1]...)` and `new ArrayList_from(array)`.

8. `matchTypes(type [, type]...)`
Returns true if the list of arguments in the **current function** matches the types passed to it.

## WORK IN PROGRESS
This framework is a work in progress and always will be.
Remember, doesn't matter how good a framework looks and feels, it's still PHP and that alone is "ewww" enough.

Mail me at spark.crz(at)gmail.com if you wanna chat!

[logo]: https://raw.githubusercontent.com/SparK-Cruz/elpho/master/logo.png
