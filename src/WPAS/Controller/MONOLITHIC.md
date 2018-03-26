## Monolithic Controler

1. Setup your routing rules
```php
use \WPAS\Controller\WPASController;
$controller = new WPASController([
    //options (optional)
]);

//Using a "Blog" controller class to fetch the information needed for the "news" category
$controller->route([ 'slug' => 'Category:news', 'controller' => 'Blog']);

//You can also use a closure if you want
$controller->route([ 'slug' => 'Category:news', 'controller' => function(){

    //here goes the script to fetch for the data
    $data['variable1'] = 'Hello World';
    return $data;
}]);

```

Note: This are the options you can pass when creating the controller

```php
        $this->options = [
            'mainscript' => null, //path to the main js that will handle all JS requests
            'data' => null, //if you want to append data to the WPAS_APP object available in js
            'mainscript-requierments' => [], //if the main js requiers any other js to be loaded first
            'namespace' => '', //if you are using a controller class instrad of a closure (anonimus function)
            ];
```

2. Request all the data you need to render the template using une function
```php

//This returns a semantic array with everything
$args = wpas_get_view_data();

echo $data['variable1']; //print the variable that came from the controller

echo $data['wp_query']; //in case you need the Queried Object (default loop) it is available in the 'wp_query' key

```


## Javascript Global Variable Injection

If you want, you can inject more properties into the WPAS_APP object by using the "wpas_js_global_variables" filter, like this:

```php
		add_filter('wpas_fill_content', function($name, $data){
			$data['lang'] = 'english';
			return $data;
		},10,2);
```

Here I'm specifying that may language is english