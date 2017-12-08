NOTICE: This library is still on early development, it was tested in a few websites but I'm still working to make it extremely easy to use and very "WordPress Styled".

# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use too truggle every day.

1. Better AJAX.
2. MVC Pattern implementation (Model-View-Controller).

### Installation
```php
$ composer install alesanchezr/wpas-wordpress-dash
```


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

## Upcomming Experimental Features

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
