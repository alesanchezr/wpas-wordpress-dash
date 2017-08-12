# WPASRoleAccessManager

Manage the user access to any page, post, tag or category.


## Very simple approach

Just declare your roles and what they can access
```php
use WPAS\Roles\WPASRole;
use WPAS\Roles\WPASRoleAccessManager;

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

## More complex setup

Roles and inherit the accesses of other roles

```php
$studentRole = new WPASRole('student');
$teacherRole = new WPASRole('teacher');

$manager->allowAccessFor($teacher,[
    'parent' => $studentRole,
    'page' => ['teacher-cohorts'],
    'post' => ['bla-bla-bla','more-bla-bla-bla']
]);
```