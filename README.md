# WPAS-Route-Controller

Are you a wordpress developer? This plugin has no admin view, is just ment for developers. 

I decided to publish this library that I always use to make WordPress developments, this plugin will make your life easier, here are some of the perks:

1. **Route ajax calls to PHP methods very organized and easy:**

```php
use \WPAS\Controller\WPASController;


$controller = new WPASController([
    'namespace' => 'Breathecode\\Controller\\'
]);

$controller->routeAjax([ 'slug' => 'bclogin', 'controller' => 'Credentials', 'ajax_action' => 'Public:custom_login']);     
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
