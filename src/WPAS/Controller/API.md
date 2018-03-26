
Creating an API endpoint:

```
    use \WPAS\Controller\WPASAPIController;
    $api = new WPASAPIController([
        'application_name' => '4gwebsite',
        'version' => 1
    ]);
    
    use TF\Types\CoursePostType;
    $api->get(['path' => '/events', 'controller' => function(){
        return CoursePostType::all()->posts;
    }]);
```

Now in your browser you can test the URL:
```
GET: yourwebsite.com/wp-json/4gwebsite/v1/events
```