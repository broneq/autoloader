**Broneq/Autoloader**

Autoloader class for PHP 5.5+. You can use many autoloader classes separately.
Class has internal control of registered namespaces, so it isn't possible to load 
anything without your knowledge.   

**Usage**

composer require broneq/autoloader


*Register namespaces*
```
include 'vendor/autoload.php';
$autoloader = new \Broneq\Autoloader\Loader();
$autoloader->registerNamespace('App', __DIR__.'/app');
$autoloader->registerNamespace('SomeOtherNameSpace', __DIR__.'/otherdir');

$autoloader->register();
``` 

*Register classes*

```
$autoloader->registerClass('Some\Classname', __DIR__.'/dir/path/to/Class_name.php');
$autoloader->registerNamespace('Some\OtherClass', __DIR__.'/other/path/to/OtherClass.php');
``` 

*Register files*
```
$autoloader = new \Broneq\Autoloader\Loader();
$autoloader->registerFile('__DIR__.'/path/to/some_functions.php');
``` 

*Other features*

You can register namespaces, classes and files and don't register autoloader. Then
you can manualy load classes.
```
include 'vendor/autoload.php';
$autoloader = new \Broneq\Autoloader\Loader();
$autoloader->registerNamespace('App', __DIR__.'/app');
$autoloader->registerClass('Some\Classname', __DIR__.'/dir/path/to/Class_name.php');

$autoloader->load('\App\Some');
$autoloader->load('\Some\Classname');

new \App\Some;
```