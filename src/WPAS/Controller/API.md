# Creating API's with WordPress

Creating an API endpoint:

```php
    $api = new \WPAS\Controller\WPASAPIController([
        'application_name' => '4gwebsite',
        'version' => 1
    ]);
    
    $api->get(['path' => '/events', 'controller' => function(){
        return  TF\Types\CoursePostType::all()->posts;
    }]);
```

Now in your browser you can test the URL:
```sh
GET: yourwebsite.com/wp-json/4gwebsite/v1/events
```

You can also add new enpoints using controller classes instead of callbacks
```php
    $api = new \WPAS\Controller\WPASAPIController([
        'namespace' => 'TF\\Controller\\', //namespace where your controllers are
        ...
    ]);
    
    $api->get(['path' => '/event', 'controller' => 'APIController:getSingleEvents']);
    $api->post(['path' => '/event', 'controller' => 'APIController:editEvents']);
    $api->put(['path' => '/event', 'controller' => 'APIController:createEvents']);
    $api->delete(['path' => '/event', 'controller' => 'APIController:deleteEvents']);
```

## Aditional options

These are all the possible settings and their respective default state
```
    $api = new \WPAS\Controller\WPASAPIController([
    
        //REQUIRED
        'application_name' => '4gwebsite', // your API name (you can have several API's in the same wp installation)
        
        //REQUIRED
        'version' => 1, // version number for the api
    
        'allow-origin' => '*' // optional: the domain you want to accept requests from, all by default
        
        'allow-methods' => 'GET' // optiona: HTTP methods to allow separated by coma: GET,POST,PUT,DELETE
    ]);
```