# Controller

1. Setup your routing rules
```php
use \WPAS\Controller\WPASController;
$controller = new WPASController([
    'mainscript' => 'script.js' //the path to your main js
    //options
]);

//Using a "Blog" controller class to fetch the information needed for the "news" category
$controller->route([ 'slug' => 'Category:news', 'controller' => 'Blog']);

//You can also use a closure if you want
$controller->route([ 'slug' => 'Category:news', 'controller' => function(){

    //here goes the script to fetch for the data
    $data['variable1'] = 'Hello World';
    return $data;
]);

//Using a 'General' controller class to process the 'newsletter_signup' ajax action in 'all' views
$controller->routeAjax([ 'slug' => 'all', 'controller' => 'General:newsletter_signup' ]);    
```

1. Request all the data you need to render the template using une function
```php

//This returns a semantic array with everything
$args = wpas_get_view_data();

echo $data['variable1']; //print the variable that came from the controller

echo $data['wp_query']; //in case you need the Queried Object (default loop) it is available in the 'wp_query' key

```


2. Do your ajax call inside any enqueued javascript
```js
        var requestData = { 
            action: 'signup',
            //any other params you want to send in the request
            foo: var
        };

        $.ajax({
            action: 'post',
            url: WPAS_APP.ajax_url,
            data: requestData,
            result: function(responseData){
                console.log(responseData);
            }
        });
```

### This are the options you can pass when creating the controller

```php
        $this->options = [
            'mainscript' => null, //path to the main js that will handle all JS requests
            'data' => null, //if you want to append data to the WPAS_APP object available in js
            'mainscript-requierments' => [], //if the main js requiers any other js to be loaded first
            'namespace' => '', //if you are using a controller class instrad of a closure (anonimus function)
            ];
```

##Logging

Set WP_DEBUG_LOG = true to start logging, check the /logs directory in your wordpress root.

```php
//as a part of your wp-config.php
define('WP_DEBUG_LOG', true);
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
