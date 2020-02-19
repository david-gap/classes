**Version 1.0** (19.02.2020)

Add custom classes to your web project and configure it by a json file.
Each class have a seperate readme file with all the information about configuration & usage.

## CONFIGURATION OPTIONS
* $debug: turn debugging on/off

## USAGE
Files have to be in the same directory. Best way would be to create a folder. (in this example folder name is "config")
Copy main files (configuration.php, configuration.json) to your template and include the PHP file.
```php
require('config/configuration.php');
```
Select all classes you want to run init settings inside configuration.php on line 80. (classes with core_ do not have a init)
```php
$runClasses = array('WPimg', 'WPimgAlt');
```
