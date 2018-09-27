# Using Models

WordPress Dash has a simple interface to create Custom post Types

```php
    use \WPAS\Types\PostTypesManager;
    $postTypeManager = new PostTypesManager([
        'namespace' => '\php\Types\\'
    ]);
    //You can react a new custom post type and specify his class
    $postTypeManager->newType(['type' => 'course', 'class' => 'CoursePostType'])->register();
    
    //You can create a class for an already created post
    $postTypeManager->newType(['type' => 'post', 'class' => 'PostPostType'])->register();
    
```

You can add as many Custom Post Types as you want, but you also need to add the model to the Model/ directory

```php
    namespace php\Types;
    
    use WPAS\Types\BasePostType;
    
    class CoursePostType extends BasePostType{
    
        //you can override the initilize function  
        function initialize(){
            //whatever you code here, gets runned at the same time as functions.php
        }
        
        
        
        //any other model methods here
    }
```

Then, you can use your model inside your controller like this:
```php
    namespace php\Controllers;
    class Course{
        
        public function renderCourse(){
            
            $args = [];
            //get a particular post by id
            $args['courses'] = CoursePostType::get(1);
            
            //get all posts
            $args['courses'] = CoursePostType::all();
            
            return $args;//Always return an Array type
        }
        
    }
```
