
# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use too truggle every day.

1. MVC Pattern implementation (Model-View-Controller) in WordPress.
2. Create API's with WordPress very fast.

## Installation

1. Require the library with composer (NOTE: You must be in your Wordpress directory before running this command. The installer will attempt to create your theme in ./wp-content/<your_theme_directory_name> )
```sh
$ composer require alesanchezr/wpas-wordpress-dash:dev-master
```

2. Create a new theme using the installation script. Or select an already created theme (it will try to create the folder structure automatically)
```sh
$ php vendor/alesanchezr/wpas-wordpress-dash/run.php <your_theme_directory_name>
```

4. Update the WPASController according to your needs in functions.php
```php
use \WPAS\Controller\WPASController;
$controller = new WPASController([
        //Here you specify the path to your consollers folder
        'namespace' => 'php\\Controllers\\'
    ]);
```

**Note:** This library expects your theme to load the _vendor/autoload.php_ file in your _functions.php_. A good way of doing that is:

```php
/**
* Autoload for PHP Composer and definition of the ABSPATH
*/

//defining the absolute path for the wordpress instalation.
if ( !defined('ABSPATH') ) define('ABSPATH', dirname(__FILE__) . '/');

//including composer autoload
require ABSPATH."vendor/autoload.php";
```

If you are working with Flywheel hosting (and/or Flywheel Local), you will need to require the path in the following way:

```
if(!strpos($_SERVER['SERVER_NAME'], '.local')){
  require 'vendor/autoload.php';
}else{
  require ABSPATH . 'vendor/autoload.php';
}
```

This is due to the fact that their folder structure separates your content and plugins from the root Wordpress install after you push your site live.


## Working with the MVC Pattern

### The Models (Custom Types)

Instanciate the PostTypeManager:
```php
    $typeManager = new \WPAS\Types\PostTypesManager([
        'namespace' => '\php\Types\\' \\this will be the path to your models folder
    ]);
```
Define your type in functions.php
```php
    //You can react a new custom post type and specify his class
    $typeManager->newType(['type' => 'your_type_slug', 'class' => 'AnyPostTypeModelClass'])->register();
```
Define your type class in the types folder:
```php
    namespace php\Types;

    class AnyPostTypeModelClass extends \WPAS\Types\BasePostType{
    
        //any method here
    }
```
Note: you HAVE to extend from the BasePostType class, that is not optional.

[Continue reading about the models](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Types)

### The Controllers

Create your ***Controller*** classes and bind them to your views, pages, categories, posts, etc.
```php
//Here we are saying that we have a class Course.php with a function getCourseInfo that fetches the data needed to render any custom post tipe course
$controller->route([ 'slug' => 'Single:course', 'controller' => 'Course' ]);  
```
Our Course.php controller class will look like this:

```php
namespace php\Controllers;

class Course{
    
    public function renderCourse(){
        
        $args = [];
        $args['course'] = WP_Query(['post_type' => 'course', 'param2' => 'value2', ...);
        return $args; //Always return an Array type
    }
    
}
```
[Continue reading about implementing MVC on your wordpress](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

## Upcomming Experimental Features (Not Stable)

1. Add WordPress roles programatically.
2. Restrict Role Access to particular pages, posts, categories, etc.
3. Create and manage all your custom post types in just a few lines of code.
4. Hit 100% on the [Google Page Speed test](https://developers.google.com/speed/pagespeed/insights/).
5. Messaging notification system for the WordPress admin user, using the WordPress standards.
6. Create new [Visual Composer](https://vc.wpbakery.com/) components for your theme in just 5 lines fo code.
7. Extend [Gravity Forms](http://www.gravityforms.com/) functionality.

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
