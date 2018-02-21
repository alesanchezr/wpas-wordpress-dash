NOTICE: This library is still on early development, it was tested in a few websites but I'm still working to make it extremely easy to use and very "WordPress Styled".

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

# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use too truggle every day.

1. MVC Pattern implementation (Model-View-Controller) in WordPress.
2. Better AJAX in WordPress.

### Installation

1. Require the library with composer
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

### Working with the MVC Pattern

To create Types (

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
        return $args;//Always return an Array type
    }
    
}
```
[Continue reading about implementing MVC on your wordpress](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

### Working with AJAX

Add one MVC-like route for each AJAX request:

```php
//Using a 'General' controller class to process the 'newsletter_signup' ajax action in the page with the slug 'contact-us'
$controller->routeAjax([ 'slug' => 'Page:contact-us', 'controller' => 'General:newsletter_signup' ]);  

//Or Instead, you can use a closure if you like
$controller->routeAjax([ 'slug' => 'Category:news', 'controller' => function(){

    //here goes the script to fetch for the data
    $data['variable1'] = 'Hello World';
    return $data;
}]);
```

[Continue reading about Working with AJAX](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

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
