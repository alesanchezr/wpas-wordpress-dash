# MVC for WordPress

There are 2 ways of working with WordPress MVC:
1. API-Oriented: Create a WordPress API and consume it using React, Angular, Vue or Vanilla Javascript.
2. Monolithic: Create templates in WordPress.

## Creating an API's using MVC

Start instanciating a new WPAS_API_Controller class:

```php
    $api = new \WPAS\Controller\WPASAPIController([
        'application_name' => '4gwebsite',
        'version' => 1
    ]);
    
```
Then, start defining your API endpoints:
```php
//get,post,put,delete
$api->get(['path' => '/events', 'controller' => 'MyController:method']);

//you can use a function as controller instead of a whole class
$api->get(['path' => '/events', 'controller' => function(){
        return  TF\Types\CoursePostType::all()->posts;
    }]);
```
You can set args to document the usage of the endpoint. [Link](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#arguments)

You can set the user's capability for the endpoint. (Uses JWT wordpress [Plugin](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) 
```

$api->put(
    [ 
        'path' => '/events/rsvp/(?P<id>[\d]+)', 
        'controller' => 'EventController:registerRSVP', 
        'capability' => 'activate_plugins',
        'args' => array(
            'answer' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key){
                    return $param === 'yes' || $param === 'no';
                }
            ),
            'username' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key){
                    return is_string($param);
                }
            )
            
        )
    ]
);
```

Here is more information on how to create API enpoints using WordPress Dash
[https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller/blob/master/API.md](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller/blob/master/API.md)

If you want variables in your path, [read here](https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/#path-variables).

## MVC Monolithic approach:

Instanciate a new WPASController
```php
$controller = new \WPAS\Controller\WPASController();
```
Define what controllers will take care of what templates using the [typical WordPress logic](https://developer.wordpress.org/themes/basics/template-files/)
```php
$controller->route([ 'slug' => 'CustomPost:post_slug', 'controller' => 'MyController:method']);

//your controller could also be a callback if you want
$controller->route([ 'slug' => 'CustomPost:post_slug', 'controller' => function(){

    return $viewData;
}]);
```

Here is more information on how to create monolithics MVC websites using WordPress Dash
[https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller/blob/master/MONOLITHIC.md](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller/blob/master/MONOLITHIC.md)


### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
