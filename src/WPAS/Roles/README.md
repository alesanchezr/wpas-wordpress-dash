# WPASRoleAccessManager

Manage the user access to any page, post or category.

```php
    use WPAS\Roles\WPASRoleAccessManager;
    $manager = new WPASRoleAccessManager();
    $manager->allow(WPASRoleAccessManager::PUBLIC_SCOPE, 'restricted-page');
    $manager->allow(WPASRoleAccessManager::PUBLIC_SCOPE, 'hello-world');
    $manager->allow('administrator', 'hello-world');
```