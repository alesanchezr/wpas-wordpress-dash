NOTICE: This library is still on early development, it was tested in a few websites but I'm still working to make it extremely easy to use and very "WordPress Styled".

# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use too truggle every day.

1. MVC Pattern implementation (Model-View-Controller) in WordPress.
2. Better AJAX in WordPress.

### Installation

1. Require the library with composer
```sh
$ composer require alesanchezr/wpas-wordpress-dash:dev-master
```
(Optional) 2. Configure composer autoload functionality (composer.json). Add:
```sh
...,
"autoload":{
        "psr-0":{
            "php" : "./wp-content/themes/<theme_name>/src"
        }
    }
...
```
The suggested folder structure for the project files is:
```sh
<theme_name>/
    ...
    /src
        ...
        /php
            /Controllers
                /<controller_name>.php
                /<controller2_name2>.php
```
3. Run composer update
```sh 
$ composer update
```

4. Create a new WPASController class (functions.php)
```php
use \WPAS\Controller\WPASController;
$controller = new WPASController([
        //Here you specify the path to your consollers folder
        'namespace' => 'php\\Controllers\\'
    ]);
```

### Working with the MVC Pattern

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
