# WPAS-Route-Controller

Are you a wordpress developer? This plugin has no admin view, is just ment for developers. 

I decided to publish this library that I always use to make WordPress developments, this plugin will make your life easier, here are some of the perks:

1. **Route ajax calls to PHP methods very organized and easy:**

```php
use \WPAS\Controller\WPASController;


$controller = new WPASController([
    //options
]);

$controller->routeAjax([ 'slug' => 'bclogin', 'controller' => 'Credentials', 'ajax_action' => 'Public:custom_login']);     
```
This are the options you can pass to the WPASController

```php
        $this->options = [
            'namespace' => '', //The PHP namespace and folders in which your controllers will be located
            'data' => null, //any data you want to pass to the javascripts
            'mainscript' => null, //if you want to use the "data" option then you need to specify where is your main javascript
            'mainscript-requierments' => [] //an array with all the js needed to load before the main script (like jquery, etc.)
            ];
```


2. **Send error notifications to the user in the wordpress admin:**

```php
WPASAdminNotifier::addTransientMessage(Utils\BCNotification::ERROR,$e->getMessage());
```

## Options

When intanciating a new WPASController you can to specify the following options:

| Option | Required | Description  |
|-----------|-------|----------------------------------------------------------|
| namespace | true  |   PHP namespace in which all your controller classes are goign to be declared |

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-route-controller](https://github.com/alesanchezr/wpas-route-controller)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
