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