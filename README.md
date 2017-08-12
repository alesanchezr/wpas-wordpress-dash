# WPAS-Wordpress-Dash

Are you a WordPress developer? Then you are probably struggling with the same stuff that I use tos truggle every day:

I decided to publish this library that I always use to make WordPress developments (themes/plugins), here are some of the perks:

## [WPASController](./tree/master/src/WPAS/Controller)

class to ROUT AJAX request very easy.

```php
//for each ajax request you want to make, define one routeAjax call
$controller->routeAjax([ 
    'slug' => 'bclogin', //the view slug in wich is going to be used
    'action' => 'signup', //a unique name to ID this request
    'controller' => function(){
        //This function will be called to process the request
        //add here any logic you want
        WPASController::ajaxSuccess($responseData); //send response back to client
     }
]);     
```

## [WPASAdminNotifier](./tree/master/src/WPAS/Messaging)

Send notifications to the admin user very easy

```php
//To notify an error, add this anywhere you want, on any hooks or class
WPASAdminNotifier::addTransientMessage(Utils\BCNotification::ERROR,'There has been an error');

//Add this at the end of your functions PHP
WPASAdminNotifier::loadTransientMessages();
```
## [WPASRole](./tree/master/src/WPAS/Roles): 

Add roles to wordpress programatically

```php
$student = new WPASRole('student');
```

## [WPASRoleAccessManager](./tree/master/src/WPAS/Roles)

Control what pages, posts, categories or tags can be accessed by each role

```php
$manager = new WPASRoleAccessManager();//instanciate the manager

    $manager->allowDefaultAccess([
        'page'=> ['hello-world'] //set a default public page (or post)
    ]);
    
    //get (or create) the role
    $student = new WPASRole('subscriber'); 
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
