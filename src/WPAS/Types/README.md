# Using Models

WordPress Dash has a simple interface to create Custom post Types

```php
    use \WPAS\Types\PostTypesManager;
    $namespace = '\php\Types\\';
    
    $postTypeManager = new PostTypesManager([
        'course:'.$namespace.'CoursePostType',
        ...
    ]);
```

You can add as many Custom Post Types as you want, but you also need to add the model to the Model/ directory

```php
    namespace php\Types;
    
    use WPAS\Types\BasePostType;
    
    class CoursePostType extends BasePostType{
    
        //any method here
    }
```

Then, you can use your model inside your controller like this:
```php
    namespace php\Controllers;
    class Course{
        
        public function renderCourse(){
            
            $args = [];
            $args['courses'] = CoursePostType::get(1);
            return $args;//Always return an Array type
        }
        
    }
```