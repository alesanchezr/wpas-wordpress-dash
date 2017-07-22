# WPAS-Route-Controller

Are you a wordpress developer? This plugin has no admin view, is just ment for developers. 

I decided to publish this library that I always use to make WordPress developments, this plugin will make your life easier, here are some of the perks:

1. **Route ajax calls to PHP methods very organized and easy:**

```php
use \WPAS\Controller\WPASController;


$controller = new WPASController([
    //options
]);

$controller->routeAjax([ 
    'slug' => 'bclogin', 
    'scope' => 'public', //it can be public or private 
    'controller' => 'Public:custom_login'
    ]);     
```
This are the options you can pass to the WPASController

```php
        $this->options = [
            'mainscript' => null, //if you want to use the "data" option then you need to specify where is your main javascript
            ];
```


2. Do your ajax call inside the mainscript 
```js
        var args = { 
            action: 'signup',
            password: document.querySelector('#password').value,
            username: document.querySelector('#username').value,
            email: document.querySelector('#email').value
        };

        ajax.post(WPAS_APP.ajax_url,args,function(data){
            console.log(data);
        });
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
