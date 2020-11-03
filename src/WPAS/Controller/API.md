# Creating API's with WordPress

Creating an API endpoint:

```php
    use \WP_REST_Request;

    $api = new \WPAS\Controller\WPASAPIController([
        'application_name' => '4gwebsite',
        'version' => 1
    ]);
    
    $api->get(['path' => '/events', 'controller' => function(WP_REST_Request $request){
        return  TF\Types\CoursePostType::all()->posts;
    }]);
```

Now in your browser you can test the URL:
```sh
GET: yourwebsite.com/wp-json/4gwebsite/v1/events
```

You can also add new enpoints using controller classes instead of callbacks
```php
    use \WP_REST_Request;

    $api = new \WPAS\Controller\WPASAPIController([
        'namespace' => 'TF\\Controller\\', //namespace where your controllers are
        ...
    ]);
    
    $api->get(['path' => '/event', 'controller' => 'APIController:getSingleEvents']);
    $api->post(['path' => '/event', 'controller' => 'APIController:editEvents']);
    $api->put(['path' => '/event', 'controller' => 'APIController:createEvents']);
    $api->delete(['path' => '/event', 'controller' => 'APIController:deleteEvents']);
```

## Some other callback examples

### Single GET
```php
    use \WP_REST_Request;

    public function getSingleCourse(WP_REST_Request $request){
        return Course::get(1);
    }

    public function getAllCourse(WP_REST_Request $request){
        
        //get all posts
        $query = Course::all();
        return $query;//Always return an Array type
    }
    
    public function getCoursesByType(WP_REST_Request $request){
        
        $query = Course::all([ 'status' => 'draft' ]);
        return $query->posts;
    }
    
    public function createCourse(WP_REST_Request $request){

        $body = json_decode($request->get_body());
        
        $id = Course::create([
            'post_title'    => $body->title,
            ]);
            return $id;
        }
        
        
        /**
         * Using Custom Post types to add new properties to the course
         */
        public function getCoursesWithCustomFields(WP_REST_Request $request){
            
            $courses = [];
            $query = Course::all([ 'status' => 'draft' ]);
            foreach($query->posts as $course){
                $courses[] = array(
                    "ID" => $course->ID,
                    "post_title" => $course->post_title,
                    "schedule_type" => get_field('schedule_type', $course->ID)
                );
            }
            return $courses;
        }
```
## Aditional options

These are all the possible settings and their respective default state
```php
    $api = new \WPAS\Controller\WPASAPIController([
    
        //REQUIRED
        'application_name' => '4gwebsite', // your API name (you can have several API's in the same wp installation)
        
        //REQUIRED
        'version' => 1, // version number for the api
    
        'allow-origin' => '*' // optional: the domain you want to accept requests from, all by default
        
        'allow-methods' => 'GET' // optiona: HTTP methods to allow separated by coma: GET,POST,PUT,DELETE
    ]);
```