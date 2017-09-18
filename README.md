# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use tos truggle every day:

I decided to publish this library that I always use to make my WordPress developments (themes/plugins), here are some of the perks:

1. WPASController: Working with ajax now is very simple, and also idea to separate your logic/data from your views (MVC Pattern).
2. WPASRole: Add WordPress roles programatically.
3. WPASRoleAccessManager: Restrict Access to particular pages, posts, categories, etc.
4. PostTypesManager: Create and manage your WordPress custom post types in just a few lines of code.
5. WPASAsyncLoader: Use this class to hit 100% on the [Google Page Speed test](https://developers.google.com/speed/pagespeed/insights/).
6. WPASAdminNotifier: Messaging notification system for the WordPress admin user, using the WordPress standards.
7. WPASGravityForm: Extend the gravity forms functionality with this easy helper.
8. VCComponent: Create new Visual Composer components for your theme in just 5 lines fo code.

Here is a breaf explenation of each helper class:

## [WPASController](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Controller)

class to ROUT AJAX request very easy.

```php

//for each ajax request you want to make, define one routeAjax call
$controller->routeAjax([ 
    'slug' => 'bclogin', //the view slug in wich is going to be used
    'action' => 'signup', //a unique name to ID this request
    'controller' => function(){
        //This function will be called to process the from-end request
        //add here any logic you want
        WPASController::ajaxSuccess($responseData); //send response back to client
     }
]);     
```

## [WPASAdminNotifier](https://github.com/alesanchezr/wpas-wordpress-dash/tree/master/src/WPAS/Messaging)

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
