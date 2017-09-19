NOTICE: This library is still on early development, it was tested in a few websites but I'm still working to make it extremely easy to use and very "WordPress Styled".

# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use too truggle every day:

I decided to publish this library that I always use to make my WordPress developments (themes/plugins), here are some of the perks:

1. Working with ajax now is very simple.
2. Separate your logic/data from your views (MVC Pattern).
2. Add WordPress roles programatically.
3. Restrict Role Access to particular pages, posts, categories, etc.
4. Create and manage all your custom post types in just a few lines of code.
5. Hit 100% on the [Google Page Speed test](https://developers.google.com/speed/pagespeed/insights/).
6. Messaging notification system for the WordPress admin user, using the WordPress standards.
8. Create new [Visual Composer](https://vc.wpbakery.com/) components for your theme in just 5 lines fo code.
7. Extend [Gravity Forms](http://www.gravityforms.com/) functionality.

Here is a breaf explenation of each Helper Class:

### Working with AJAX

Add one MVC-like route for each AJAX request:

```php
//Using a 'General' controller class to process the 'newsletter_signup' ajax action in the page with the slug 'contact-us'
$controller->routeAjax([ 'slug' => 'Page:contact-us', 'controller' => 'General:newsletter_signup' ]);  

//Instead, you can use a closure if you like
$controller->routeAjax([ 'slug' => 'Category:news', 'controller' => function(){

    //here goes the script to fetch for the data
    $data['variable1'] = 'Hello World';
    return $data;
]);
```

[Continue reading about Working with AJAX](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

### Simple MVC Pattern

Create ***Controller*** classes and bind them to your views, pages, categories, posts, etc.

```php
//Here we are saying that we have a class Course.php with a function getCourseInfo that fetches the data needed to render any custom post tipe course
$controller->route([ 'slug' => 'Single:course', 'controller' => 'Course' ]);  
```
Our Course.php controller will look like this:

```php
class Course{
    
    public function getCourseInfo(){
        
        $args = [];
        $args['course'] = WP_Query(['post_type' => 'course', 'param2' => 'value2', ...);
        return $args;
    }
    
}
```
[Continue reading about implementing MVC on your wordpress](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

### [WPASAdminNotifier](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Messaging)

Send notifications to the admin user very easy

```php
use WPAS\Messaging\WPASAdminNotifier;
//To notify an error, add this anywhere you want, on any hooks or class
WPASAdminNotifier::addTransientMessage(WPASAdminNotifier::ERROR,'Hey admin! There has been an error');

//Add this at the end of your functions PHP
WPASAdminNotifier::loadTransientMessages();
```
## [WPASRole](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Roles): 

Add roles to wordpress programatically

```php
$student = new WPAS\Roles\WPASRole('student');
```

## [WPASRoleAccessManager](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Roles)

Control what pages, posts, categories or tags can be accessed by each role

```php
$manager = new WPAS\Roles\WPASRoleAccessManager();//instanciate the manager

    $manager->allowDefaultAccess([
        'page'=> ['hello-world'] //set a default public page (or post)
    ]);
    
    //get (or create) the role
    $student = new WPAS\Roles\WPASRole('subscriber'); 
    //set the slugs that the role will have access to
    $manager->allowAccessFor($student,[
        'page' => ['restricted-page', 'hello-world'],
        'category' => ['courses']
    ]);
```

### Author

**Alejandro Sanchez**

  *Repository website:* [https://github.com/alesanchezr/wpas-wordpress-dash](https://github.com/alesanchezr/wpas-wordpress-dash)
  
  *About me:* [alesanchezr.com](http://alesanchezr.com)
