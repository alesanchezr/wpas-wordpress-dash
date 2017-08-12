# WPAS-Wordpress-Dash

Are you a wordpress developer? This plugin has no admin view, is just ment for developers. 

I decided to publish this library that I always use to make WordPress developments (themes/plugins), this plugin will make your life easier, here are some of the perks:

1. **Route ajax calls to PHP methods very organized and easy:**

```php
use \WPAS\Controller\WPASController;


$controller = new WPASController([
    'mainscript' => 'script.js' //the path to your main js
    //options
]);

//for each ajax request you want to make, define one routeAjax call
$controller->routeAjax([ 
    'slug' => 'bclogin', //the view slug in wich is going to be used
    'scope' => 'public', // (optional) if the user needs to be signed in
    'action' => 'signup', //a unique name to ID this request
    'controller' => function(){
        //This function will be called to process the request
        //add here any logic you want
        WPASController::ajaxSuccess($responseData); //send response back to client
     }
]);     
```
2. Do your ajax call inside the mainscript 
```js
        var requestData = { 
            action: 'signup',
            //any other params you want to send in the request
        };

        ajax.post(WPAS_APP.ajax_url,requestData,function(responseData){
            console.log(responseData);
        });
```

###This are the options you can pass when creating the controller

```php
        $this->options = [
            'mainscript' => null, //path to the main js that will handle all JS requests
            'data' => null, //if you want to append data to the WPAS_APP object available in js
            'mainscript-requierments' => [], //if the main js requiers any other js to be loaded first
            'namespace' => '', //if you are using a controller class instrad of a closure (anonimus function)
            ];
```

## Options

When intanciating a new WPASController you can to specify the following options:

| Option                            | Default   | Description  |
|-----------------------------------|-----------|----------------------------------------------------------|
| namespace (required)              | ''        | PHP namespace in which all your controller classes are goign to be declared |
| data (optional)                   | []        | any data you want to append to the data array |
| mainscript-requierments (optinal) | ''        | ['script1',] |
| namespace (optional)              | ''        | PHP namespace in which all your controller classes are goign to be declared |

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
